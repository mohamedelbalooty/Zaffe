<?php

namespace App\Services;

use InvalidArgumentException;

/**
 * Pure-PHP ISO/IEC 18004 Byte-mode QR Code SVG encoder (Versions 1-10, ECC Level M).
 * Produces compact vector SVG output with a 4-module white quiet zone and zero external dependencies.
 */
class QrCodeService
{
    private const QUIET_ZONE = 4;

    /**
     * Version specs at ECC Level M:
     * [maxBytes, totalCodewords, ecPerBlock, g1Blocks, g1Data, g2Blocks, g2Data, alignmentCenters, remainderBits]
     *
     * @var array<int, array{0:int,1:int,2:int,3:int,4:int,5:int,6:int,7:int[],8:int}>
     */
    private const VERSION_SPECS = [
        1 => [14, 26, 10, 1, 16, 0, 0, [], 0],
        2 => [26, 44, 16, 1, 28, 0, 0, [6, 18], 7],
        3 => [42, 70, 26, 1, 44, 0, 0, [6, 22], 7],
        4 => [62, 100, 18, 2, 32, 0, 0, [6, 26], 7],
        5 => [84, 134, 24, 2, 43, 0, 0, [6, 30], 7],
        6 => [106, 172, 16, 4, 27, 0, 0, [6, 34], 7],
        7 => [122, 196, 18, 4, 31, 0, 0, [6, 22, 38], 0],
        8 => [152, 242, 22, 2, 38, 2, 39, [6, 24, 42], 0],
        9 => [180, 292, 22, 3, 36, 2, 37, [6, 26, 46], 0],
        10 => [213, 346, 26, 4, 43, 1, 44, [6, 28, 50], 0],
    ];

    /** @var int[] */
    private static array $gfExp = [];

    /** @var int[] */
    private static array $gfLog = [];

    private static function initGaloisField(): void
    {
        if (self::$gfExp !== []) {
            return;
        }

        $x = 1;
        for ($i = 0; $i < 255; $i++) {
            self::$gfExp[$i] = $x;
            self::$gfLog[$x] = $i;
            $x <<= 1;
            if ($x & 0x100) {
                $x ^= 0x11D;
            }
        }

        for ($i = 255; $i < 512; $i++) {
            self::$gfExp[$i] = self::$gfExp[$i - 255];
        }
    }

    private static function gfMul(int $a, int $b): int
    {
        if ($a === 0 || $b === 0) {
            return 0;
        }

        return self::$gfExp[self::$gfLog[$a] + self::$gfLog[$b]];
    }

    /**
     * Compute ISO/IEC 18004 QR boolean matrix for arbitrary UTF-8 string up to 213 bytes.
     *
     * @return array<int, array<int, bool>>
     */
    public function encodeMatrix(string $payload): array
    {
        $bytes = array_values(unpack('C*', $payload) ?: []);
        $len = count($bytes);

        if ($len === 0) {
            throw new InvalidArgumentException('QR payload cannot be empty.');
        }

        $version = $this->selectVersion($len);
        $spec = self::VERSION_SPECS[$version];
        $size = 17 + ($version * 4);

        $codewords = $this->buildCodewords($bytes, $version, $spec);
        $bits = $this->interleaveAndEncodeBits($codewords, $spec);

        $grid = array_fill(0, $size, array_fill(0, $size, false));
        $reserved = array_fill(0, $size, array_fill(0, $size, false));

        $this->placeFunctionPatterns($grid, $reserved, $size, $version, $spec[7]);
        $this->placeDataBits($grid, $reserved, $size, $bits);

        $bestMask = 0;
        $bestScore = PHP_INT_MAX;
        $bestGrid = $grid;

        for ($mask = 0; $mask < 8; $mask++) {
            $candidate = $this->applyMask($grid, $reserved, $size, $mask);
            $this->placeFormatInfo($candidate, $size, $mask);
            $score = $this->evaluatePenalty($candidate, $size);

            if ($score < $bestScore) {
                $bestScore = $score;
                $bestMask = $mask;
                $bestGrid = $candidate;
            }
        }

        if ($version >= 7) {
            $this->placeVersionInfo($bestGrid, $size, $version);
        }

        $this->placeFormatInfo($bestGrid, $size, $bestMask);

        return $bestGrid;
    }

    public function renderSvg(
        string $payload,
        int $sizePx = 220,
        string $darkColor = '#111827',
        string $lightColor = '#FFFFFF',
        string $ariaLabel = 'QR Entry Pass',
    ): string {
        $matrix = $this->encodeMatrix($payload);
        $dim = count($matrix);
        $totalModules = $dim + (self::QUIET_ZONE * 2);

        $pathParts = [];
        for ($r = 0; $r < $dim; $r++) {
            $c = 0;
            while ($c < $dim) {
                if (! $matrix[$r][$c]) {
                    $c++;

                    continue;
                }

                $runStart = $c;
                while ($c < $dim && $matrix[$r][$c]) {
                    $c++;
                }

                $x = $runStart + self::QUIET_ZONE;
                $y = $r + self::QUIET_ZONE;
                $w = $c - $runStart;
                $pathParts[] = "M{$x},{$y}h{$w}v1h-{$w}z";
            }
        }

        $path = implode('', $pathParts);
        $safeDark = htmlspecialchars($darkColor, ENT_QUOTES, 'UTF-8');
        $safeLight = htmlspecialchars($lightColor, ENT_QUOTES, 'UTF-8');
        $safeLabel = htmlspecialchars($ariaLabel, ENT_QUOTES, 'UTF-8');

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %1$d %1$d" width="%2$d" height="%2$d" shape-rendering="crispEdges" role="img" aria-label="%3$s"><rect width="%1$d" height="%1$d" fill="%4$s"/><path d="%5$s" fill="%6$s"/></svg>',
            $totalModules,
            $sizePx,
            $safeLabel,
            $safeLight,
            $path,
            $safeDark,
        );
    }

    private function selectVersion(int $byteLength): int
    {
        foreach (self::VERSION_SPECS as $version => $spec) {
            if ($byteLength <= $spec[0]) {
                return $version;
            }
        }

        throw new InvalidArgumentException('QR payload exceeds maximum supported 213-byte capacity.');
    }

    /**
     * @param  int[]  $bytes
     * @param  array{0:int,1:int,2:int,3:int,4:int,5:int,6:int,7:int[],8:int}  $spec
     * @return int[]
     */
    private function buildCodewords(array $bytes, int $version, array $spec): array
    {
        $totalDataCodewords = ($spec[3] * $spec[4]) + ($spec[5] * $spec[6]);
        $bitBuffer = '';

        // Byte mode indicator = 0100
        $bitBuffer .= '0100';

        // Character count indicator: 8 bits for V1-9, 16 bits for V10+
        $countBits = $version >= 10 ? 16 : 8;
        $bitBuffer .= str_pad(decbin(count($bytes)), $countBits, '0', STR_PAD_LEFT);

        foreach ($bytes as $b) {
            $bitBuffer .= str_pad(decbin($b), 8, '0', STR_PAD_LEFT);
        }

        $maxDataBits = $totalDataCodewords * 8;
        $terminatorLen = min(4, $maxDataBits - strlen($bitBuffer));
        $bitBuffer .= str_repeat('0', max(0, $terminatorLen));

        $remainderMod = strlen($bitBuffer) % 8;
        if ($remainderMod !== 0) {
            $bitBuffer .= str_repeat('0', 8 - $remainderMod);
        }

        $codewords = [];
        for ($i = 0; $i < strlen($bitBuffer); $i += 8) {
            $codewords[] = bindec(substr($bitBuffer, $i, 8));
        }

        $padBytes = [0xEC, 0x11];
        $padIndex = 0;
        while (count($codewords) < $totalDataCodewords) {
            $codewords[] = $padBytes[$padIndex % 2];
            $padIndex++;
        }

        return $codewords;
    }

    /**
     * @param  int[]  $dataCodewords
     * @param  array{0:int,1:int,2:int,3:int,4:int,5:int,6:int,7:int[],8:int}  $spec
     * @return int[]
     */
    private function interleaveAndEncodeBits(array $dataCodewords, array $spec): array
    {
        self::initGaloisField();

        $ecCount = $spec[2];
        $g1Blocks = $spec[3];
        $g1Data = $spec[4];
        $g2Blocks = $spec[5];
        $g2Data = $spec[6];
        $remainderBits = $spec[8];

        $generator = $this->rsGeneratorPoly($ecCount);

        $dataBlocks = [];
        $ecBlocks = [];
        $offset = 0;

        for ($i = 0; $i < $g1Blocks; $i++) {
            $block = array_slice($dataCodewords, $offset, $g1Data);
            $offset += $g1Data;
            $dataBlocks[] = $block;
            $ecBlocks[] = $this->rsComputeEc($block, $generator, $ecCount);
        }

        for ($i = 0; $i < $g2Blocks; $i++) {
            $block = array_slice($dataCodewords, $offset, $g2Data);
            $offset += $g2Data;
            $dataBlocks[] = $block;
            $ecBlocks[] = $this->rsComputeEc($block, $generator, $ecCount);
        }

        $interleaved = [];
        $maxDataLen = max($g1Data, $g2Data);

        for ($col = 0; $col < $maxDataLen; $col++) {
            foreach ($dataBlocks as $block) {
                if (isset($block[$col])) {
                    $interleaved[] = $block[$col];
                }
            }
        }

        for ($col = 0; $col < $ecCount; $col++) {
            foreach ($ecBlocks as $ec) {
                $interleaved[] = $ec[$col];
            }
        }

        $bits = [];
        foreach ($interleaved as $byte) {
            for ($b = 7; $b >= 0; $b--) {
                $bits[] = ($byte >> $b) & 1;
            }
        }

        for ($i = 0; $i < $remainderBits; $i++) {
            $bits[] = 0;
        }

        return $bits;
    }

    /**
     * @return int[]
     */
    private function rsGeneratorPoly(int $degree): array
    {
        $poly = [1];

        for ($i = 0; $i < $degree; $i++) {
            $factor = [1, self::$gfExp[$i]];
            $next = array_fill(0, count($poly) + 1, 0);

            foreach ($poly as $j => $pVal) {
                foreach ($factor as $k => $fVal) {
                    $next[$j + $k] ^= self::gfMul($pVal, $fVal);
                }
            }

            $poly = $next;
        }

        return $poly;
    }

    /**
     * @param  int[]  $data
     * @param  int[]  $generator
     * @return int[]
     */
    private function rsComputeEc(array $data, array $generator, int $ecCount): array
    {
        $buffer = array_merge($data, array_fill(0, $ecCount, 0));

        for ($i = 0; $i < count($data); $i++) {
            $coef = $buffer[$i];
            if ($coef === 0) {
                continue;
            }

            for ($j = 0; $j <= $ecCount; $j++) {
                $buffer[$i + $j] ^= self::gfMul($generator[$j], $coef);
            }
        }

        return array_slice($buffer, count($data));
    }

    /**
     * @param  array<int, array<int, bool>>  $grid
     * @param  array<int, array<int, bool>>  $reserved
     * @param  int[]  $alignmentCenters
     */
    private function placeFunctionPatterns(array &$grid, array &$reserved, int $size, int $version, array $alignmentCenters): void
    {
        // Finder patterns + separators (top-left, top-right, bottom-left)
        $this->placeFinderPattern($grid, $reserved, $size, 0, 0);
        $this->placeFinderPattern($grid, $reserved, $size, 0, $size - 7);
        $this->placeFinderPattern($grid, $reserved, $size, $size - 7, 0);

        // Alignment patterns
        $centerCount = count($alignmentCenters);
        for ($i = 0; $i < $centerCount; $i++) {
            for ($j = 0; $j < $centerCount; $j++) {
                $r = $alignmentCenters[$i];
                $c = $alignmentCenters[$j];

                // Skip locations overlapping finder corners
                if (($r <= 8 && $c <= 8)
                    || ($r <= 8 && $c >= $size - 8)
                    || ($r >= $size - 8 && $c <= 8)) {
                    continue;
                }

                $this->placeAlignmentPattern($grid, $reserved, $r, $c);
            }
        }

        // Timing patterns along row 6 and col 6
        for ($i = 8; $i < $size - 8; $i++) {
            $dark = ($i % 2) === 0;

            $grid[6][$i] = $dark;
            $reserved[6][$i] = true;

            $grid[$i][6] = $dark;
            $reserved[$i][6] = true;
        }

        // Fixed dark module
        $grid[$size - 8][8] = true;
        $reserved[$size - 8][8] = true;

        // Reserve Format Info areas so data placement skips them
        for ($i = 0; $i < 9; $i++) {
            if ($i !== 6) {
                $reserved[8][$i] = true;
                $reserved[$i][8] = true;
            }
        }
        for ($i = $size - 8; $i < $size; $i++) {
            $reserved[8][$i] = true;
        }
        for ($i = $size - 7; $i < $size; $i++) {
            $reserved[$i][8] = true;
        }

        // Reserve and write Version Info for V7+
        if ($version >= 7) {
            for ($k = 0; $k < 6; $k++) {
                for ($j = 0; $j < 3; $j++) {
                    $reserved[$size - 11 + $j][$k] = true;
                    $reserved[$k][$size - 11 + $j] = true;
                }
            }
            $this->placeVersionInfo($grid, $size, $version);
        }
    }

    /**
     * @param  array<int, array<int, bool>>  $grid
     * @param  array<int, array<int, bool>>  $reserved
     */
    private function placeFinderPattern(array &$grid, array &$reserved, int $size, int $topRow, int $leftCol): void
    {
        for ($dr = -1; $dr <= 7; $dr++) {
            for ($dc = -1; $dc <= 7; $dc++) {
                $r = $topRow + $dr;
                $c = $leftCol + $dc;
                if ($r < 0 || $r >= $size || $c < 0 || $c >= $size) {
                    continue;
                }

                $reserved[$r][$c] = true;
                if ($dr === -1 || $dr === 7 || $dc === -1 || $dc === 7) {
                    $grid[$r][$c] = false;
                } elseif ($dr === 0 || $dr === 6 || $dc === 0 || $dc === 6 || ($dr >= 2 && $dr <= 4 && $dc >= 2 && $dc <= 4)) {
                    $grid[$r][$c] = true;
                } else {
                    $grid[$r][$c] = false;
                }
            }
        }
    }

    /**
     * @param  array<int, array<int, bool>>  $grid
     * @param  array<int, array<int, bool>>  $reserved
     */
    private function placeAlignmentPattern(array &$grid, array &$reserved, int $centerRow, int $centerCol): void
    {
        for ($dr = -2; $dr <= 2; $dr++) {
            for ($dc = -2; $dc <= 2; $dc++) {
                $r = $centerRow + $dr;
                $c = $centerCol + $dc;
                $reserved[$r][$c] = true;
                $grid[$r][$c] = (max(abs($dr), abs($dc)) !== 1);
            }
        }
    }

    /**
     * @param  array<int, array<int, bool>>  $grid
     * @param  array<int, array<int, bool>>  $reserved
     * @param  int[]  $bits
     */
    private function placeDataBits(array &$grid, array $reserved, int $size, array $bits): void
    {
        $bitIdx = 0;
        $bitCount = count($bits);
        $upward = true;
        $col = $size - 1;

        while ($col >= 1) {
            if ($col === 6) {
                $col--;
            }

            for ($step = 0; $step < $size; $step++) {
                $row = $upward ? ($size - 1 - $step) : $step;

                for ($offset = 0; $offset < 2; $offset++) {
                    $c = $col - $offset;
                    if (! $reserved[$row][$c]) {
                        $grid[$row][$c] = ($bitIdx < $bitCount) && ($bits[$bitIdx] === 1);
                        $bitIdx++;
                    }
                }
            }

            $upward = ! $upward;
            $col -= 2;
        }
    }

    /**
     * @param  array<int, array<int, bool>>  $grid
     * @param  array<int, array<int, bool>>  $reserved
     * @return array<int, array<int, bool>>
     */
    private function applyMask(array $grid, array $reserved, int $size, int $mask): array
    {
        $out = $grid;

        for ($r = 0; $r < $size; $r++) {
            for ($c = 0; $c < $size; $c++) {
                if ($reserved[$r][$c]) {
                    continue;
                }

                $invert = match ($mask) {
                    0 => (($r + $c) % 2) === 0,
                    1 => ($r % 2) === 0,
                    2 => ($c % 3) === 0,
                    3 => (($r + $c) % 3) === 0,
                    4 => ((intdiv($r, 2) + intdiv($c, 3)) % 2) === 0,
                    5 => ((($r * $c) % 2) + (($r * $c) % 3)) === 0,
                    6 => (((($r * $c) % 2) + (($r * $c) % 3)) % 2) === 0,
                    default => (((($r + $c) % 2) + (($r * $c) % 3)) % 2) === 0,
                };

                if ($invert) {
                    $out[$r][$c] = ! $out[$r][$c];
                }
            }
        }

        return $out;
    }

    /**
     * @param  array<int, array<int, bool>>  $grid
     */
    private function placeFormatInfo(array &$grid, int $size, int $mask): void
    {
        // ECC M = 00
        $data = $mask & 0x7;
        $rem = $data << 10;
        for ($bit = 14; $bit >= 10; $bit--) {
            if (($rem >> $bit) & 1) {
                $rem ^= (0x537 << ($bit - 10));
            }
        }

        $formatBits = (($data << 10) | $rem) ^ 0x5412;

        // Place first copy around top-left finder
        $topLeftCols = [0, 1, 2, 3, 4, 5, 7, 8];
        foreach ($topLeftCols as $idx => $col) {
            $bitVal = (($formatBits >> (14 - $idx)) & 1) === 1;
            $grid[8][$col] = $bitVal;
        }
        $topLeftRows = [7, 5, 4, 3, 2, 1, 0];
        foreach ($topLeftRows as $idx => $row) {
            $bitVal = (($formatBits >> (6 - $idx)) & 1) === 1;
            $grid[$row][8] = $bitVal;
        }

        // Place second copy around bottom-left and top-right finders
        for ($i = 0; $i < 7; $i++) {
            $bitVal = (($formatBits >> (14 - $i)) & 1) === 1;
            $grid[$size - 1 - $i][8] = $bitVal;
        }
        for ($i = 0; $i < 8; $i++) {
            $bitVal = (($formatBits >> (7 - $i)) & 1) === 1;
            $grid[8][$size - 8 + $i] = $bitVal;
        }
    }

    /**
     * @param  array<int, array<int, bool>>  $grid
     */
    private function placeVersionInfo(array &$grid, int $size, int $version): void
    {
        $rem = $version << 12;
        for ($bit = 17; $bit >= 12; $bit--) {
            if (($rem >> $bit) & 1) {
                $rem ^= (0x1F25 << ($bit - 12));
            }
        }

        $versionBits = ($version << 12) | $rem;

        for ($k = 0; $k < 6; $k++) {
            for ($j = 0; $j < 3; $j++) {
                $bitVal = (($versionBits >> (($k * 3) + $j)) & 1) === 1;
                $grid[$size - 11 + $j][$k] = $bitVal;
                $grid[$k][$size - 11 + $j] = $bitVal;
            }
        }
    }

    /**
     * @param  array<int, array<int, bool>>  $grid
     */
    private function evaluatePenalty(array $grid, int $size): int
    {
        $penalty = 0;

        // N1: 5+ consecutive same-color modules in rows & cols
        for ($r = 0; $r < $size; $r++) {
            $rowRun = 1;
            $colRun = 1;
            for ($c = 1; $c < $size; $c++) {
                if ($grid[$r][$c] === $grid[$r][$c - 1]) {
                    $rowRun++;
                } else {
                    if ($rowRun >= 5) {
                        $penalty += $rowRun - 2;
                    }
                    $rowRun = 1;
                }

                if ($grid[$c][$r] === $grid[$c - 1][$r]) {
                    $colRun++;
                } else {
                    if ($colRun >= 5) {
                        $penalty += $colRun - 2;
                    }
                    $colRun = 1;
                }
            }
            if ($rowRun >= 5) {
                $penalty += $rowRun - 2;
            }
            if ($colRun >= 5) {
                $penalty += $colRun - 2;
            }
        }

        // N2: 2x2 uniform blocks
        for ($r = 0; $r < $size - 1; $r++) {
            for ($c = 0; $c < $size - 1; $c++) {
                $v = $grid[$r][$c];
                if ($v === $grid[$r][$c + 1] && $v === $grid[$r + 1][$c] && $v === $grid[$r + 1][$c + 1]) {
                    $penalty += 3;
                }
            }
        }

        // N3: 1:1:3:1:1 finder-like pattern with 4 light modules on either side
        for ($r = 0; $r < $size; $r++) {
            for ($c = 0; $c <= $size - 11; $c++) {
                if ($this->matchesN3Row($grid, $r, $c)) {
                    $penalty += 40;
                }
                if ($this->matchesN3Col($grid, $c, $r)) {
                    $penalty += 40;
                }
            }
        }

        // N4: dark ratio deviation from 50%
        $dark = 0;
        for ($r = 0; $r < $size; $r++) {
            for ($c = 0; $c < $size; $c++) {
                if ($grid[$r][$c]) {
                    $dark++;
                }
            }
        }
        $percent = (int) (($dark * 100) / ($size * $size));
        $prevMultiple = intdiv($percent, 5) * 5;
        $nextMultiple = $prevMultiple + 5;
        $penalty += min(abs($prevMultiple - 50), abs($nextMultiple - 50)) * 2;

        return $penalty;
    }

    /**
     * @param  array<int, array<int, bool>>  $grid
     */
    private function matchesN3Row(array $grid, int $r, int $c): bool
    {
        $pattern1 = [true, false, true, true, true, false, true, false, false, false, false];
        $pattern2 = [false, false, false, false, true, false, true, true, true, false, true];

        $match1 = true;
        $match2 = true;
        for ($i = 0; $i < 11; $i++) {
            $val = $grid[$r][$c + $i];
            if ($val !== $pattern1[$i]) {
                $match1 = false;
            }
            if ($val !== $pattern2[$i]) {
                $match2 = false;
            }
        }

        return $match1 || $match2;
    }

    /**
     * @param  array<int, array<int, bool>>  $grid
     */
    private function matchesN3Col(array $grid, int $r, int $c): bool
    {
        $pattern1 = [true, false, true, true, true, false, true, false, false, false, false];
        $pattern2 = [false, false, false, false, true, false, true, true, true, false, true];

        $match1 = true;
        $match2 = true;
        for ($i = 0; $i < 11; $i++) {
            $val = $grid[$r + $i][$c];
            if ($val !== $pattern1[$i]) {
                $match1 = false;
            }
            if ($val !== $pattern2[$i]) {
                $match2 = false;
            }
        }

        return $match1 || $match2;
    }
}
