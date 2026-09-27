# samirhv — S essencial, edition 02

A personal identity for Samir Hanna Verza: precise, authored and direct. This edition refines the selected cyan S into a consistent vector system. **Proposal only: no production website assets or styles have been replaced.** The original study remains in `../s-essencial/`.

The yellow revision adds a pure `#FFFF00` hairline to the upper terminal of the S. Its two-unit horizontal width on the 100-unit source grid is 2.5% of the visible symbol width. It stays inside the existing silhouette, follows the terminal angle and appears nowhere else in the mark. The name and supporting graphics retain their original colors. The previous cyan-only revision is preserved in Git commit `4e1978c`.

Start with `overview.png` and the ten-page `manual-identidade.pdf`. Page 05 specifies the yellow accent, with its digital color values, geometry and usage rules. The Portuguese manual is intended for the brand owner and future application designers.

## Files

- `assets/logo-horizontal-{dark,light,white,black}.svg`: primary signatures; dark/light names identify the intended background. Transparent PNG companions are included.
- `assets/logo-stacked-*`: compact signatures. `wordmark-*` contains the name alone.
- `assets/symbol-*`: standalone symbol. `symbol-mono.svg` inherits CSS `currentColor`.
- `assets/favicon.svg`, `favicon-light.svg`, `favicon-{16,32,48}.png`, `favicon.ico`: optically simplified symbol on a background plate; ICO includes all three sizes.
- `assets/app-icon-{cyan,dark}-*.png`: rounded avatar and app previews, 192, 512 and 1024 pixels.
- `assets/mobile-icon-1024.png`: opaque, square mobile source for platform masking. `apple-touch-icon.png` is 180 pixels.
- `assets/social-cover.*`: 1200 × 630 social composition. `repository-cover.*`: 1280 × 640 repository cover.
- `assets/graphic-ribbon.svg`: optional diagonal motif; use sparingly.
- `identity.json` and `tokens.css`: portable reference values, not active application configuration.
- `fonts/`: original Manrope variable font, derived static weights and SIL Open Font License. Source and SHA-256 are recorded in `identity.json`.
- `references/direction.png`: the supplied direction, retained for provenance; not a production asset.

## Visual rules

Keep the name lowercase. The wordmark uses outlined Manrope at weight 750 with custom spacing; use supplied artwork instead of retyping it. Titles use weight 700, labels 500, body text 400. Reserve monospaced type for actual code and technical values.

| Color | Value | Role |
| --- | --- | --- |
| Cyan | `#0BB8EE` | Symbol and highlights on dark surfaces |
| Graphite | `#0B1018` | Main dark surface and light-theme text |
| Mist | `#F4F7FA` | Main light surface and dark-theme text |
| Muted | `#9BA9BA` | Secondary text on graphite |
| Cyan ink | `#007A9E` | Small colored text on mist |
| Pure yellow | `#FFFF00` | Tiny upper-terminal accent only |

Cyan on graphite has 8.3:1 contrast; mist on graphite 17.7:1; cyan ink on mist 4.6:1. Do not use bright cyan for small text on light surfaces. Logo colors are graphic identity, not automatic text colors.

Clearspace is at least one quarter of the visible symbol height, measured from the artwork bounds. Suggested digital minimums: horizontal SVG canvas width 160 px; standalone visible symbol height 24 px. For smaller use, choose the dedicated favicon. Confirm legibility in the actual product context when applying.

No glow, gradients, shadows, stretching, extra outlines or rotation. Preserve flat colors. Use the diagonal motif once per composition and keep it away from reading areas. Favor generous whitespace and simple alignment over decorative panels.

Keep yellow confined to the supplied terminal in full-color marks. Do not repeat it on the lower terminal, the wordmark, backgrounds or ribbons. Monochrome signatures deliberately omit it. The optical favicon uses a 0.75-unit inset on its 16-unit grid so that the accent survives rasterization without becoming a large block. The accent is decorative and does not convey status or other information.

Yellow color specification: sRGB HEX `#FFFF00`, RGB `255 / 255 / 0`, HSL `60° / 100% / 50%`, 100% opacity. The source terminal is 2 units wide horizontally and 15 units tall on the 100-unit grid; its horizontal width is 2.5% of the 80-unit visible S width. Follow the original terminal slope. The larger swatch on the specification page documents the color and is not a suggested application area.

The proposed verbal signature is **“Engenharia com assinatura.”** Supporting copy: **“Projetos, ferramentas e código.”** Describe concrete problems, working software and how to get started; avoid inflated promises.

## Rebuild

Python dependencies: fonttools, brotli, reportlab, svglib, Pillow. Node dependency: sharp. Install these in an isolated environment; no application dependencies need changing.

```sh
python build-vectors.py
node export.cjs
python build-overview.py
node -e "require('sharp')('overview.svg').png().toFile('overview.png')"
python build-manual.py
```

Run from this directory. The vector wordmarks contain paths, not external fonts or raster references. The manual embeds fonts and uses vector marks; the social application preview is rasterized to preserve its opacity. All actual artwork remains editable in SVG. Exported PNG logos have transparent backgrounds; the app/mobile variants explicitly supply a background.

The review package is assembled from the files in this directory, excluding itself. It is a handoff convenience; the individual files remain canonical.
