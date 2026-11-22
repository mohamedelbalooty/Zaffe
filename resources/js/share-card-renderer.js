function hexToRgb(hex) {
    const clean = String(hex || '').replace('#', '').trim();
    if (!/^[0-9a-fA-F]{6}$/.test(clean)) {
        return { r: 34, g: 30, b: 26 };
    }
    return {
        r: parseInt(clean.slice(0, 2), 16),
        g: parseInt(clean.slice(2, 4), 16),
        b: parseInt(clean.slice(4, 6), 16),
    };
}

function rgba(hex, alpha) {
    const { r, g, b } = hexToRgb(hex);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

function luminance(hex) {
    const { r, g, b } = hexToRgb(hex);
    const chan = [r, g, b].map((v) => {
        const s = v / 255;
        return s <= 0.03928 ? s / 12.92 : Math.pow((s + 0.055) / 1.055, 2.4);
    });
    return 0.2126 * chan[0] + 0.7152 * chan[1] + 0.0722 * chan[2];
}

function contrastRatio(hexA, hexB) {
    const lumA = luminance(hexA);
    const lumB = luminance(hexB);
    return (Math.max(lumA, lumB) + 0.05) / (Math.min(lumA, lumB) + 0.05);
}

function pickHighContrastColor(preferredHex, fallbackHex, bgHex, minRatio = 3.2) {
    if (contrastRatio(preferredHex, bgHex) >= minRatio) {
        return preferredHex;
    }
    return fallbackHex;
}

function drawRoundedRect(ctx, x, y, w, h, r) {
    const radius = Math.min(r, w / 2, h / 2);
    ctx.beginPath();
    ctx.moveTo(x + radius, y);
    ctx.lineTo(x + w - radius, y);
    ctx.quadraticCurveTo(x + w, y, x + w, y + radius);
    ctx.lineTo(x + w, y + h - radius);
    ctx.quadraticCurveTo(x + w, y + h, x + w - radius, y + h);
    ctx.lineTo(x + radius, y + h);
    ctx.quadraticCurveTo(x, y + h, x, y + h - radius);
    ctx.lineTo(x, y + radius);
    ctx.quadraticCurveTo(x, y, x + radius, y);
    ctx.closePath();
}

function drawDiamond(ctx, cx, cy, size, fill, stroke = null, lineWidth = 1.5) {
    ctx.save();
    ctx.beginPath();
    ctx.moveTo(cx, cy - size);
    ctx.lineTo(cx + size, cy);
    ctx.lineTo(cx, cy + size);
    ctx.lineTo(cx - size, cy);
    ctx.closePath();
    if (fill) {
        ctx.fillStyle = fill;
        ctx.fill();
    }
    if (stroke) {
        ctx.strokeStyle = stroke;
        ctx.lineWidth = lineWidth;
        ctx.stroke();
    }
    ctx.restore();
}

function resolveCanvasFontFamily(fontFamily) {
    switch (fontFamily) {
        case 'Cairo':
            return '"Cairo", "Tajawal", sans-serif';
        case 'Amiri':
            return '"Amiri", "Tajawal", serif';
        case 'Playfair Display':
            return '"Playfair Display", "Cairo", Georgia, serif';
        default:
            return '"Tajawal", "Cairo", sans-serif';
    }
}

async function ensureFontsLoaded() {
    if (!document.fonts || typeof document.fonts.load !== 'function') {
        return;
    }

    const fontSpecs = [
        '700 44px "Amiri"',
        '900 60px "Cairo"',
        '700 28px "Tajawal"',
        '700 48px "Playfair Display"',
    ];

    const timeout = new Promise((resolve) => setTimeout(resolve, 1800));
    await Promise.race([
        Promise.allSettled([
            ...fontSpecs.map((spec) => document.fonts.load(spec)),
            document.fonts.ready,
        ]),
        timeout,
    ]);
}

async function loadOptionalImage(src) {
    if (!src) return null;

    return new Promise((resolve) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        const timer = setTimeout(() => resolve(null), 4000);
        img.onload = () => {
            clearTimeout(timer);
            resolve(img.naturalWidth > 0 ? img : null);
        };
        img.onerror = () => {
            clearTimeout(timer);
            resolve(null);
        };
        img.src = src;
    });
}

function fitFontSize(ctx, text, fontWeight, fontFamily, startSize, minSize, maxWidth) {
    let size = startSize;
    while (size > minSize) {
        ctx.font = `${fontWeight} ${size}px ${fontFamily}`;
        if (ctx.measureText(text).width <= maxWidth) {
            return size;
        }
        size -= 2;
    }
    return minSize;
}

function wrapLines(ctx, text, maxWidth, maxLines = 2) {
    const clean = String(text || '').trim();
    if (!clean) return [];

    const words = clean.split(/\s+/);
    const lines = [];
    let current = '';

    for (const word of words) {
        const candidate = current ? `${current} ${word}` : word;
        if (ctx.measureText(candidate).width <= maxWidth || !current) {
            current = candidate;
        } else {
            lines.push(current);
            current = word;
            if (lines.length === maxLines - 1) {
                continue;
            }
        }
    }

    if (current) {
        if (lines.length >= maxLines) {
            lines[maxLines - 1] = `${lines[maxLines - 1]} ${current}`;
        } else {
            lines.push(current);
        }
    }

    if (lines.length > maxLines) {
        lines.length = maxLines;
    }

    const lastIdx = lines.length - 1;
    if (lastIdx >= 0 && ctx.measureText(lines[lastIdx]).width > maxWidth) {
        let trimmed = lines[lastIdx];
        while (trimmed.length > 3 && ctx.measureText(`${trimmed}…`).width > maxWidth) {
            trimmed = trimmed.slice(0, -1).trim();
        }
        lines[lastIdx] = `${trimmed}…`;
    }

    return lines;
}

function drawCrownCrest(ctx, cx, cy, color) {
    ctx.save();
    ctx.strokeStyle = color;
    ctx.fillStyle = rgba(color, 0.14);
    ctx.lineWidth = 2.6;
    ctx.lineJoin = 'round';
    ctx.beginPath();
    ctx.moveTo(cx - 26, cy + 12);
    ctx.lineTo(cx - 32, cy - 10);
    ctx.lineTo(cx - 14, cy + 2);
    ctx.lineTo(cx, cy - 18);
    ctx.lineTo(cx + 14, cy + 2);
    ctx.lineTo(cx + 32, cy - 10);
    ctx.lineTo(cx + 26, cy + 12);
    ctx.closePath();
    ctx.fill();
    ctx.stroke();

    ctx.beginPath();
    ctx.moveTo(cx - 24, cy + 20);
    ctx.lineTo(cx + 24, cy + 20);
    ctx.stroke();
    ctx.restore();
}

function drawThemeWatermarkAndFrame(ctx, theme, W, H, coverImg) {
    const ornament = theme.ornament_style || 'royal_crest';
    const primary = theme.primary_color;
    const accent = theme.accent_color || primary;
    const cardBg = theme.card_bg;
    const isDark = Boolean(theme.is_dark_theme);

    const cardX = 36;
    const cardY = 36;
    const cardW = W - 72;
    const cardH = H - 72;

    // Outer ambient gradient backdrop
    ctx.fillStyle = theme.canvas_bg;
    ctx.fillRect(0, 0, W, H);

    const bgGlow = ctx.createRadialGradient(W / 2, H * 0.25, 40, W / 2, H * 0.45, W * 0.78);
    bgGlow.addColorStop(0, rgba(accent, isDark ? 0.14 : 0.12));
    bgGlow.addColorStop(1, rgba(primary, 0));
    ctx.fillStyle = bgGlow;
    ctx.fillRect(0, 0, W, H);

    // Stationery card body with drop shadow
    ctx.save();
    ctx.shadowColor = isDark ? 'rgba(0, 0, 0, 0.55)' : 'rgba(21, 18, 16, 0.16)';
    ctx.shadowBlur = 44;
    ctx.shadowOffsetY = 18;
    drawRoundedRect(ctx, cardX, cardY, cardW, cardH, 28);
    ctx.fillStyle = cardBg;
    ctx.fill();
    ctx.restore();

    // Clip decorative fills neatly inside the stationery card
    ctx.save();
    drawRoundedRect(ctx, cardX, cardY, cardW, cardH, 28);
    ctx.clip();

    // Subtle luxury surface sheen inside card
    const cardSheen = ctx.createRadialGradient(W / 2, H * 0.36, 40, W / 2, H * 0.45, W * 0.7);
    cardSheen.addColorStop(0, isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(255, 255, 255, 0.72)');
    cardSheen.addColorStop(1, rgba(theme.card_bg_alt || cardBg, isDark ? 0.45 : 0.52));
    ctx.fillStyle = cardSheen;
    ctx.fillRect(cardX, cardY, cardW, cardH);

    // Theme-specific subtle watermarks inside card
    if (ornament === 'sadu_lozenge') {
        // Al Sadu woven geometric top & bottom bands
        const bandH = 22;
        const drawBand = (by) => {
            ctx.fillStyle = primary;
            ctx.fillRect(cardX, by, cardW, bandH);
            for (let bx = cardX + 18; bx < cardX + cardW - 10; bx += 36) {
                ctx.beginPath();
                ctx.moveTo(bx, by + 3);
                ctx.lineTo(bx + 14, by + bandH / 2);
                ctx.lineTo(bx, by + bandH - 3);
                ctx.lineTo(bx - 14, by + bandH / 2);
                ctx.closePath();
                ctx.fillStyle = cardBg;
                ctx.fill();
                drawDiamond(ctx, bx, by + bandH / 2, 4.5, accent);
            }
        };
        drawBand(cardY);
        drawBand(cardY + cardH - bandH);

        ctx.strokeStyle = rgba(primary, 0.045);
        ctx.lineWidth = 1.2;
        for (let gx = 90; gx < W - 60; gx += 120) {
            for (let gy = 140; gy < H - 100; gy += 120) {
                drawDiamond(ctx, gx, gy, 32, null, rgba(primary, 0.04), 1.2);
            }
        }
    } else if (ornament === 'desert_sun_dunes') {
        // Diagonal sunlit silk ribbons & smooth dune horizon
        ctx.fillStyle = 'rgba(253, 248, 242, 0.09)';
        ctx.beginPath();
        ctx.moveTo(cardX, cardY);
        ctx.lineTo(420, cardY);
        ctx.lineTo(cardX + cardW, 760);
        ctx.lineTo(cardX + cardW, cardY + cardH);
        ctx.lineTo(460, cardY + cardH);
        ctx.lineTo(cardX, 500);
        ctx.closePath();
        ctx.fill();

        ctx.strokeStyle = 'rgba(232, 195, 158, 0.14)';
        ctx.lineWidth = 1.6;
        ctx.beginPath();
        ctx.moveTo(cardX, H - 190);
        ctx.bezierCurveTo(320, H - 255, 740, H - 140, cardX + cardW, H - 210);
        ctx.stroke();
    } else if (ornament === 'najdi_triangles') {
        // Stepped Najdi rosette watermark centered softly
        ctx.save();
        ctx.strokeStyle = rgba(primary, 0.065);
        ctx.lineWidth = 1.6;
        ctx.beginPath();
        ctx.arc(W / 2, H * 0.48, 210, 0, Math.PI * 2);
        ctx.stroke();
        drawDiamond(ctx, W / 2, H * 0.48, 235, null, rgba(primary, 0.055), 1.4);
        ctx.save();
        ctx.translate(W / 2, H * 0.48);
        ctx.rotate(Math.PI / 4);
        ctx.strokeStyle = rgba(accent, 0.065);
        ctx.strokeRect(-145, -145, 290, 290);
        ctx.restore();
        ctx.restore();
    } else if (ornament === 'hijazi_roshan') {
        // Subtle Rawashin geometric diamond lattice watermark
        ctx.strokeStyle = rgba(primary, 0.042);
        ctx.lineWidth = 1.2;
        for (let gy = 120; gy < H - 100; gy += 92) {
            for (let gx = 100; gx < W - 80; gx += 92) {
                drawDiamond(ctx, gx, gy, 24, null, rgba(primary, 0.045), 1);
            }
        }
    } else if (ornament === 'constellation') {
        // Midnight starfield speckles
        const stars = [
            [140, 170], [920, 190], [210, 320], [870, 340], [160, 1080], [910, 1100], [540, 118],
        ];
        for (const [sx, sy] of stars) {
            ctx.fillStyle = rgba(primary, 0.25);
            ctx.beginPath();
            ctx.arc(sx, sy, 2.6, 0, Math.PI * 2);
            ctx.fill();
        }
    } else if (ornament === 'islamic_star') {
        ctx.save();
        ctx.translate(W / 2, H * 0.48);
        ctx.strokeStyle = rgba(primary, 0.055);
        ctx.lineWidth = 1.5;
        ctx.strokeRect(-155, -155, 310, 310);
        ctx.rotate(Math.PI / 4);
        ctx.strokeRect(-155, -155, 310, 310);
        ctx.restore();
    } else if (ornament === 'taif_rose_bloom' || ornament === 'blooming_flowers') {
        // Soft botanical corner blooms
        for (const [bx, by] of [[cardX + 40, cardY + 40], [cardX + cardW - 40, cardY + cardH - 40]]) {
            const bloom = ctx.createRadialGradient(bx, by, 10, bx, by, 180);
            bloom.addColorStop(0, rgba(primary, 0.14));
            bloom.addColorStop(1, rgba(primary, 0));
            ctx.fillStyle = bloom;
            ctx.beginPath();
            ctx.arc(bx, by, 180, 0, Math.PI * 2);
            ctx.fill();
        }
    } else if (ornament === 'pearl_shell') {
        ctx.strokeStyle = rgba(primary, 0.08);
        ctx.lineWidth = 1.5;
        for (const radius of [160, 210, 260]) {
            ctx.beginPath();
            ctx.arc(W / 2, 120, radius, 0.15 * Math.PI, 0.85 * Math.PI);
            ctx.stroke();
        }
    }

    ctx.restore();

    // Inner perimeter hairline frame (universal stationery refinement)
    const inset = 64;
    const frameX = inset;
    const frameY = inset;
    const frameW = W - inset * 2;
    const frameH = H - inset * 2;
    const frameColor = ornament === 'minimal_dots' ? rgba(primary, 0.28) : rgba(accent, isDark ? 0.42 : 0.55);

    ctx.save();
    drawRoundedRect(ctx, frameX, frameY, frameW, frameH, 18);
    ctx.strokeStyle = frameColor;
    ctx.lineWidth = ornament === 'royal_crest' || ornament === 'wax_seal' ? 2 : 1.5;
    ctx.stroke();

    if (ornament !== 'minimal_dots') {
        drawRoundedRect(ctx, frameX + 10, frameY + 10, frameW - 20, frameH - 20, 12);
        ctx.strokeStyle = rgba(primary, isDark ? 0.22 : 0.22);
        ctx.lineWidth = 1;
        ctx.stroke();
    }

    // Corner diamonds on non-minimal templates
    if (ornament !== 'minimal_dots') {
        const corners = [
            [frameX, frameY],
            [frameX + frameW, frameY],
            [frameX, frameY + frameH],
            [frameX + frameW, frameY + frameH],
        ];
        for (const [cx, cy] of corners) {
            drawDiamond(ctx, cx, cy, 7, cardBg, accent, 1.8);
        }
    }
    ctx.restore();

    // Top centerpiece emblem
    const crestY = 138;
    ctx.save();
    ctx.strokeStyle = rgba(accent, 0.55);
    ctx.lineWidth = 1.5;
    ctx.beginPath();
    ctx.moveTo(W / 2 - 150, crestY);
    ctx.lineTo(W / 2 - 54, crestY);
    ctx.moveTo(W / 2 + 54, crestY);
    ctx.lineTo(W / 2 + 150, crestY);
    ctx.stroke();

    if (ornament === 'royal_crest' || ornament === 'wax_seal') {
        drawCrownCrest(ctx, W / 2, crestY - 2, accent);
    } else if (ornament === 'couture_monogram') {
        ctx.beginPath();
        ctx.arc(W / 2, crestY, 26, 0, Math.PI * 2);
        ctx.fillStyle = cardBg;
        ctx.fill();
        ctx.strokeStyle = accent;
        ctx.lineWidth = 1.8;
        ctx.stroke();
        drawDiamond(ctx, W / 2, crestY, 7, accent);
    } else {
        drawDiamond(ctx, W / 2, crestY, 11, rgba(primary, 0.14), accent, 1.8);
        drawDiamond(ctx, W / 2 - 22, crestY, 4.5, accent);
        drawDiamond(ctx, W / 2 + 22, crestY, 4.5, accent);
    }
    ctx.restore();
}

function drawCoverMedallion(ctx, img, cx, cy, radius, borderColor, bgFill) {
    ctx.save();
    ctx.beginPath();
    ctx.arc(cx, cy, radius + 9, 0, Math.PI * 2);
    ctx.strokeStyle = rgba(borderColor, 0.5);
    ctx.lineWidth = 1.5;
    ctx.stroke();

    ctx.beginPath();
    ctx.arc(cx, cy, radius + 3, 0, Math.PI * 2);
    ctx.fillStyle = bgFill;
    ctx.fill();
    ctx.strokeStyle = borderColor;
    ctx.lineWidth = 2.6;
    ctx.stroke();

    ctx.beginPath();
    ctx.arc(cx, cy, radius, 0, Math.PI * 2);
    ctx.clip();

    // Object-cover crop inside medallion
    const scale = Math.max((radius * 2) / img.naturalWidth, (radius * 2) / img.naturalHeight);
    const drawW = img.naturalWidth * scale;
    const drawH = img.naturalHeight * scale;
    ctx.drawImage(img, cx - drawW / 2, cy - drawH / 2, drawW, drawH);
    ctx.restore();
}

export async function renderShareCard(canvas, payload) {
    if (!canvas || !payload) {
        return null;
    }

    await ensureFontsLoaded();

    const W = Number(payload.dimensions?.width || 1080);
    const H = Number(payload.dimensions?.height || 1350);
    canvas.width = W;
    canvas.height = H;

    const ctx = canvas.getContext('2d');
    if (!ctx) {
        return null;
    }

    const inv = payload.invitation || {};
    const theme = payload.theme || {};
    const copy = payload.copy || {};
    const isRtl = inv.dir !== 'ltr';
    const isDark = Boolean(theme.is_dark_theme);
    const cardBg = theme.card_bg || '#FFFFFF';
    const primary = theme.primary_color || '#C6A76B';
    const accent = theme.accent_color || primary;
    const inkColor = theme.ink_color || (isDark ? '#FDF8F2' : '#292521');
    const mutedInk = theme.muted_ink_color || (isDark ? '#E8C39E' : '#5C534A');

    const nameColor = pickHighContrastColor(primary, inkColor, cardBg, 3.3);
    const headingAccent = pickHighContrastColor(primary, accent, cardBg, 2.8);
    const dateAccent = pickHighContrastColor(accent, inkColor, cardBg, 3.0);

    const primaryFontStack = resolveCanvasFontFamily(theme.font_family);
    const cairoStack = '"Cairo", "Tajawal", sans-serif';
    const amiriStack = '"Amiri", "Tajawal", serif';

    const coverImg = await loadOptionalImage(inv.cover_photo);

    ctx.clearRect(0, 0, W, H);
    ctx.textBaseline = 'middle';
    ctx.textAlign = 'center';
    ctx.direction = isRtl ? 'rtl' : 'ltr';

    drawThemeWatermarkAndFrame(ctx, theme, W, H, coverImg);

    // 1. Blessing & Invitation Eyebrow
    const blessingText = copy.blessing || '';
    if (blessingText) {
        const blessingSize = fitFontSize(ctx, blessingText, 700, amiriStack, 36, 24, 840);
        ctx.font = `700 ${blessingSize}px ${amiriStack}`;
        ctx.fillStyle = headingAccent;
        ctx.fillText(blessingText, W / 2, 208, 860);
    }

    const eyebrowText = copy.eyebrow || '';
    if (eyebrowText) {
        const eyebrowSize = fitFontSize(ctx, eyebrowText, 700, cairoStack, 25, 19, 840);
        ctx.font = `700 ${eyebrowSize}px ${cairoStack}`;
        ctx.fillStyle = rgba(mutedInk, 0.92);
        ctx.fillText(eyebrowText, W / 2, 258, 860);
    }

    // 2. Optional Cover Medallion + Vertical Layout Budget
    let coupleBaseY = 408;
    let detailsY = 668;
    let detailsH = 382;

    if (coverImg) {
        drawCoverMedallion(ctx, coverImg, W / 2, 372, 74, accent, cardBg);
        coupleBaseY = 516;
        detailsY = 740;
        detailsH = 330;
    } else {
        // Subtle ornamental divider before couple names
        ctx.save();
        ctx.strokeStyle = rgba(accent, 0.35);
        ctx.lineWidth = 1.2;
        ctx.beginPath();
        ctx.moveTo(W / 2 - 110, 314);
        ctx.lineTo(W / 2 - 14, 314);
        ctx.moveTo(W / 2 + 14, 314);
        ctx.lineTo(W / 2 + 110, 314);
        ctx.stroke();
        drawDiamond(ctx, W / 2, 314, 5, accent);
        ctx.restore();
    }

    // 3. Couple Names Block (Arabic RTL: Groom Right [x=760], Bride Left [x=320])
    const groomParts = inv.groom_parts || { first: inv.groom_name || '', lineage: '' };
    const brideParts = inv.bride_parts || { first: inv.bride_name || '', lineage: '' };
    const hasBride = Boolean(inv.has_bride_name && (brideParts.first || inv.bride_name));

    const drawPersonColumn = (cx, label, parts, maxWidth, firstMaxSize) => {
        ctx.font = `700 22px ${cairoStack}`;
        ctx.fillStyle = rgba(mutedInk, 0.85);
        ctx.fillText(label, cx, coupleBaseY, maxWidth);

        const firstName = parts.first || '';
        const firstSize = fitFontSize(ctx, firstName, 900, primaryFontStack, firstMaxSize, 32, maxWidth);
        ctx.font = `900 ${firstSize}px ${primaryFontStack}`;
        ctx.fillStyle = nameColor;
        ctx.fillText(firstName, cx, coupleBaseY + 62, maxWidth);

        const lineage = parts.lineage || '';
        if (lineage) {
            ctx.font = `700 24px ${cairoStack}`;
            const lineageLines = wrapLines(ctx, lineage, maxWidth, 2);
            ctx.fillStyle = rgba(mutedInk, 0.92);
            lineageLines.forEach((line, idx) => {
                ctx.fillText(line, cx, coupleBaseY + 122 + idx * 34, maxWidth);
            });
        }
    };

    if (hasBride) {
        const groomX = isRtl ? 758 : 322;
        const brideX = isRtl ? 322 : 758;
        const colMaxW = 330;

        drawPersonColumn(groomX, copy.groom_label || 'العريس', groomParts, colMaxW, 56);
        drawPersonColumn(brideX, copy.bride_label || 'العروس', brideParts, colMaxW, 56);

        // Center vertical divider + separator ('و' / '&')
        const sepY = coupleBaseY + 62;
        ctx.save();
        ctx.strokeStyle = rgba(accent, 0.42);
        ctx.lineWidth = 1.5;
        ctx.beginPath();
        ctx.moveTo(W / 2, coupleBaseY + 8);
        ctx.lineTo(W / 2, sepY - 30);
        ctx.moveTo(W / 2, sepY + 30);
        ctx.lineTo(W / 2, coupleBaseY + 132);
        ctx.stroke();

        ctx.font = `700 44px ${amiriStack}`;
        ctx.fillStyle = headingAccent;
        ctx.fillText(copy.separator || (isRtl ? 'و' : '&'), W / 2, sepY);
        ctx.restore();
    } else {
        drawPersonColumn(W / 2, copy.groom_label || 'العريس', groomParts, 760, 68);
    }

    // 4. Date, Umm al-Qura Hijri, Time & Venue Panel (dynamically centered around actual rows)
    const boxX = 114;
    const boxW = W - 228;
    const panelInnerW = boxW - 72;

    const gregorian = inv.gregorian_date || '';
    const hijriText = inv.hijri_date ? (isRtl ? `الموافق ${inv.hijri_date}` : inv.hijri_date) : '';
    const timeText = inv.time ? (isRtl ? `الساعة ${inv.time}` : `At ${inv.time}`) : '';

    let venueLines = [];
    let venueSize = 29;
    if (inv.venue_name) {
        venueSize = fitFontSize(ctx, inv.venue_name, 800, cairoStack, 29, 22, boxW - 88);
        ctx.font = `800 ${venueSize}px ${cairoStack}`;
        venueLines = wrapLines(ctx, inv.venue_name, boxW - 88, 2);
    }

    let contentSpan = 0;
    if (gregorian) contentSpan += 44;
    if (hijriText) contentSpan += 42;
    if (timeText) contentSpan += 38;
    if (venueLines.length > 0) {
        contentSpan += (contentSpan > 0 ? 44 : 0) + (venueLines.length * 38);
    }

    const panelCenterY = detailsY + detailsH / 2;
    const actualBoxH = Math.max(200, Math.min(detailsH, contentSpan + 84));
    const actualBoxY = Math.round(panelCenterY - actualBoxH / 2);

    ctx.save();
    drawRoundedRect(ctx, boxX, actualBoxY, boxW, actualBoxH, 22);
    ctx.fillStyle = isDark ? 'rgba(0, 0, 0, 0.2)' : rgba(theme.canvas_bg || '#FAF7F2', 0.72);
    ctx.fill();
    ctx.strokeStyle = rgba(accent, isDark ? 0.34 : 0.4);
    ctx.lineWidth = 1.4;
    ctx.stroke();
    ctx.restore();

    let cursorY = actualBoxY + (actualBoxH - contentSpan) / 2 + 18;

    // Gregorian Date
    if (gregorian) {
        const gSize = fitFontSize(ctx, gregorian, 800, cairoStack, 30, 21, panelInnerW);
        ctx.font = `800 ${gSize}px ${cairoStack}`;
        ctx.fillStyle = inkColor;
        ctx.fillText(gregorian, W / 2, cursorY, panelInnerW);
        cursorY += 44;
    }

    // Hijri Date (Umm al-Qura)
    if (hijriText) {
        const hSize = fitFontSize(ctx, hijriText, 700, cairoStack, 25, 19, panelInnerW);
        ctx.font = `700 ${hSize}px ${cairoStack}`;
        ctx.fillStyle = dateAccent;
        ctx.fillText(hijriText, W / 2, cursorY, panelInnerW);
        cursorY += 42;
    }

    // Event Time
    if (timeText) {
        ctx.font = `700 24px ${cairoStack}`;
        ctx.fillStyle = rgba(mutedInk, 0.95);
        ctx.fillText(timeText, W / 2, cursorY, panelInnerW);
        cursorY += 38;
    }

    // Divider + Venue Name
    if (venueLines.length > 0) {
        if (gregorian || hijriText || timeText) {
            cursorY += 4;
            ctx.save();
            ctx.strokeStyle = rgba(accent, 0.35);
            ctx.lineWidth = 1.2;
            ctx.beginPath();
            ctx.moveTo(boxX + 90, cursorY);
            ctx.lineTo(boxX + boxW - 90, cursorY);
            ctx.stroke();
            ctx.restore();
            cursorY += 40;
        }

        ctx.font = `800 ${venueSize}px ${cairoStack}`;
        ctx.fillStyle = inkColor;
        venueLines.forEach((line, idx) => {
            ctx.fillText(line, W / 2, cursorY + idx * 38, boxW - 88);
        });
    }

    // 5. Interactive Live-Invitation CTA Pill + Brand Footer
    const pillW = 790;
    const pillH = 60;
    const pillX = (W - pillW) / 2;
    const pillY = 1118;

    ctx.save();
    drawRoundedRect(ctx, pillX, pillY, pillW, pillH, 30);
    ctx.fillStyle = isDark ? rgba(primary, 0.16) : rgba(primary, 0.1);
    ctx.fill();
    ctx.strokeStyle = rgba(primary, isDark ? 0.45 : 0.36);
    ctx.lineWidth = 1.5;
    ctx.stroke();

    const badgeText = copy.cta_badge || '';
    const badgeSize = fitFontSize(ctx, badgeText, 800, cairoStack, 21, 16, pillW - 48);
    ctx.font = `800 ${badgeSize}px ${cairoStack}`;
    ctx.fillStyle = nameColor;
    ctx.fillText(badgeText, W / 2, pillY + pillH / 2 + 1, pillW - 44);
    ctx.restore();

    if (copy.brand_footer) {
        ctx.font = `700 20px ${cairoStack}`;
        ctx.fillStyle = rgba(mutedInk, 0.72);
        ctx.fillText(copy.brand_footer, W / 2, 1228, 600);
    }

    return canvas;
}

export function canvasToPngBlob(canvas) {
    return new Promise((resolve, reject) => {
        if (!canvas || typeof canvas.toBlob !== 'function') {
            reject(new Error('Canvas export is unavailable in this browser.'));
            return;
        }
        canvas.toBlob((blob) => {
            if (blob) {
                resolve(blob);
            } else {
                reject(new Error('Failed to encode share card PNG.'));
            }
        }, 'image/png');
    });
}

if (typeof window !== 'undefined') {
    window.ZaffeShareCard = {
        render: renderShareCard,
        toBlob: canvasToPngBlob,
    };
}
