---
name: frontend-design
description: >-
  Create distinctive, production-grade frontend interfaces with high design quality.
  Use this skill when building or enhancing web pages, components, layouts, or luxury
  invitation templates to avoid generic templated aesthetics and deliver intentional
  typography, color craft, spatial hierarchy, and culturally grounded motion.
---

# Frontend Design Skill

This skill guides creation of distinctive, production-grade frontend interfaces that avoid generic "AI slop" aesthetics. Implement real working code with exceptional attention to aesthetic details, cultural authenticity, and creative craft.

Before writing UI markup or CSS, read the project's domain aesthetic specification:
- [Zaffe Luxury Design System Reference](./references/zaffe-luxury-design-system.md)

## Design Thinking

Before coding, understand the context and commit to a **BOLD aesthetic direction**:

1. **Purpose & Cultural Context**: What problem does this interface solve, who uses it, and where? For Zaffe (زفّــة), primary users are engaged couples and wedding guests in **Saudi Arabia (KSA)** and **Egypt** opening invitations inside **WhatsApp's mobile WebView** over 4G.
2. **Tone**: Commit to an unmistakable aesthetic direction—such as *Arabian Royal Editorial*, *Parchment & Wax Seal*, *Botanical Courtyard*, *Andalusian Calligraphy*, *Nocturnal Obsidian & Gold*, or *Swiss Architectural Minimalism*—never timid, washed-out SaaS defaults.
3. **Constraints**: Respect technical & product realities (Laravel 13 + Livewire 4 + Alpine.js + Tailwind CSS v4, RTL Arabic-first layout with LTR English support, `< 2.5s` mobile LCP, zero external hotlinked images, SVG-only icons via `<x-app-icon>`).
4. **Differentiation**: Define the unforgettable signature detail of every screen—e.g. a tactile royal wax-seal open-gate (`شاشة فتح الدعوة`), dual Umm al-Qura Hijri + Gregorian date plaques, or editorial contrast between monumental display names and hairline gold ornament rules.

## Aesthetics Guidelines

Focus on craft across five dimensions:

### 1. Typography
Choose fonts that are distinctive, expressive, and culturally appropriate—never default blindly to generic system stacks (`Inter`, `Roboto`, `Arial`) for hero or ceremonial surfaces:
- **Ceremonial & Quranic / Poetic Flourishes**: `Amiri` (`font-amiri`)
- **Monumental Arabic Display Headlines**: `Cairo` (`font-cairo`, tight tracking, bold contrast)
- **Clean Readable Arabic UI & Body**: `Tajawal` (`font-tajawal`, comfortable line-height)
- **Latin Editorial Counterpart**: `Playfair Display` (`font-playfair`)
- Pair dramatic scale contrast (monumental couple names or hero display headlines vs. crisp uppercase/spaced metadata labels and `<bdi>` directional isolation for numbers/dates).

### 2. Color & Atmospheric Surfaces
Commit to a cohesive, high-contrast palette with one dominant aesthetic mood and razor-sharp accents:
- **Warm Parchment & Champagne Gold**: `--color-ivory` (`#FAF7F2`), `--color-charcoal` (`#292521`), `--color-gold` (`#C6A76B`), `--color-beige` (`#E8DED0`).
- **Deep Obsidian Night / Emerald Sanctuary**: `#0F172A` / `#0F4C3A` paired with luminous gold highlights (`#E6C687`, `#C6A76B`).
- **Atmosphere & Depth**: Use subtle noise/parchment grain textures, radial gold auras, layered warm elevation shadows (`.shadow-wedding`, `.shadow-royal-card`), and delicate double-hairline ornamental frames—never flat white boxes stacked mindlessly or clichéd purple-to-blue gradients.

### 3. Spatial Composition & Rhythm
- Break monotony through asymmetrical editorial hero grids, overlapping seal/crest medallions, framed inner borders, and deliberate negative space that lets Arabic calligraphy breathe.
- Design for **375px–430px WhatsApp mobile WebViews first**, scaling gracefully to tablet and desktop without horizontal overflow or hover-only interactions.

### 4. Motion & Micro-interactions
- Use purposeful CSS transitions and Alpine.js micro-interactions for high-leverage moments: the ceremony open-gate reveal (`شاشة فتح الدعوة`), live preview mode toggles, smooth tab transitions, and button tactile elevation.
- Always respect `@media (prefers-reduced-motion: reduce)` and keep animations GPU-friendly (`transform`, `opacity`) to protect mobile battery and frame rate.

### 5. Component Completeness & Culturally Authentic Craft
- Explicitly handle **empty, photo-free, draft, and submitted states** gracefully—especially Saudi privacy-by-default invitations where couple photos or bride full names may be intentionally omitted.
- Use `<x-app-icon>` crisp SVGs exclusively inside luxury templates—**zero Unicode emojis** inside ceremonial cards.
- Use **formal-simple Arabic (الفصحى البسيطة / البيضاء)** and self-hosted `.webp` or SVG/CSS visual compositions—**zero external hotlinked URLs**.

## Anti-Patterns ("AI Slop" to Reject)
- Generic SaaS hero templates with purple/cyan gradients and stock-photo dependency.
- Unstyled default forms, uniform icon-box grids without editorial hierarchy, or emojis standing in for bespoke SVG ornament.
- Hardcoded LTR assumptions (`ml-`, `text-left` without RTL awareness) or broken bidirectional date/time mixing (always wrap dates/times in `<bdi>`).
- Ignoring empty/privacy states (broken `<img>` frames when zero photos are uploaded).
