"""Build outlined samirhv identity assets. Requires fontTools and brotli."""
from pathlib import Path
import json, hashlib
from fontTools.ttLib import TTFont
from fontTools.varLib.instancer import instantiateVariableFont
from fontTools.pens.svgPathPen import SVGPathPen
from fontTools.pens.transformPen import TransformPen
from fontTools.pens.boundsPen import BoundsPen
ROOT=Path(__file__).resolve().parent
A=ROOT/'assets';F=ROOT/'fonts'
CYAN='#0BB8EE';NAVY='#0B1018';WHITE='#F4F7FA';INK='#007A9E';MUTED='#9BA9BA';YELLOW='#FFFF00'
SHAPE='M90 10H36C20 10 10 21 10 36C10 45 15 51 24 55L62 73Q66 75 62 75H17.5L10 90H64C80 90 90 79 90 64C90 55 85 49 76 45L38 27Q34 25 38 25H82.5Z'
# A two-unit inset follows the existing terminal. It never extends the silhouette.
ACCENT='M88 10H90L82.5 25H80.5Z'
for weight in [400,500,600,700,750]:
    font=instantiateVariableFont(TTFont(F/'Manrope.ttf'),{'wght':weight},inplace=True)
    for rec in font['name'].names:
        if rec.nameID in (1,4,6): rec.string=f'Manrope-{weight}'.encode(rec.getEncoding())
    font.save(F/f'Manrope-{weight}.ttf')
font=TTFont(F/'Manrope-750.ttf');glyphs=font.getGlyphSet();cmap=font.getBestCmap();upem=font['head'].unitsPerEm
scale=76/upem;x=0;parts=[];bounds=[]
for ch in 'samirhv':
    name=cmap[ord(ch)];pen=SVGPathPen(glyphs);glyphs[name].draw(TransformPen(pen,(scale,0,0,-scale,x,0)))
    bp=BoundsPen(glyphs);glyphs[name].draw(TransformPen(bp,(scale,0,0,-scale,x,0)))
    if bp.bounds: bounds.append(bp.bounds)
    parts.append(pen.getCommands());x+=font['hmtx'][name][0]*scale-.9
xmin=min(b[0] for b in bounds);ymin=min(b[1] for b in bounds);xmax=max(b[2] for b in bounds);ymax=max(b[3] for b in bounds)
wordWidth=xmax-xmin;wordHeight=ymax-ymin
wordPath=' '.join(parts)
# Equal visible top/bottom margins around the actual outlines, including the i dot.
word=lambda color,x=0,y=0: f'<g fill="{color}" transform="translate({x-xmin:.4f} {y-ymin:.4f})"><path d="{wordPath}"/></g>'
def mark(color, accent=False):
    body=f'<path d="{SHAPE}" fill="{color}"/>'
    return body+(f'<path d="{ACCENT}" fill="{YELLOW}"/>' if accent else '')
def svg(w,h,body,label):return f'<svg xmlns="http://www.w3.org/2000/svg" width="{w}" height="{h}" viewBox="0 0 {w} {h}" role="img" aria-label="{label}">{body}</svg>\n'
def save(name,w,h,body,label='samirhv'): (A/name).write_text(svg(w,h,body,label))
width=round(120+wordWidth+10,3)
for mode,mc,wc in [('dark',CYAN,WHITE),('light',CYAN,NAVY),('white',WHITE,WHITE),('black',NAVY,NAVY)]:
    save(f'logo-horizontal-{mode}.svg',width,100,mark(mc,mode in ('dark','light'))+word(wc,120,(100-wordHeight)/2))
    save(f'logo-stacked-{mode}.svg',round(wordWidth+40,3),205,f'<g transform="translate({(wordWidth+40-100)/2} 8)">{mark(mc,mode in ("dark","light"))}</g>'+word(wc,20,132))
    save(f'wordmark-{mode}.svg',round(wordWidth+20,3),round(wordHeight+20,3),word(wc,10,10))
for name,color in [('cyan',CYAN),('navy',NAVY),('white',WHITE),('mono','currentColor')]:save(f'symbol-{name}.svg',100,100,mark(color,name=='cyan'))
for theme,bg,fg in [('cyan',CYAN,NAVY),('dark',NAVY,CYAN)]:
    save(f'app-icon-{theme}.svg',512,512,f'<rect width="512" height="512" rx="112" fill="{bg}"/><g transform="translate(56 56) scale(4)">{mark(fg,True)}</g>')
save('mobile-icon-source.svg',1024,1024,f'<rect width="1024" height="1024" fill="{CYAN}"/><g transform="translate(112 112) scale(8)">{mark(NAVY,True)}</g>')
# Optical micro variant: wider openings, reduced curve complexity, whole-pixel terminals.
micro='M14 2H6C3.5 2 2 3.8 2 6C2 7 2.7 7.8 4 8.5L10 12H3L2 14H10C12.5 14 14 12.2 14 10C14 9 13.3 8.2 12 7.5L6 4H13Z'
microAccent=f'<path d="M13.25 2H14L13 4H12.25Z" fill="{YELLOW}"/>'
save('favicon.svg',16,16,f'<rect width="16" height="16" rx="3" fill="{NAVY}"/><path d="{micro}" fill="{CYAN}"/>'+microAccent)
save('favicon-light.svg',16,16,f'<rect width="16" height="16" rx="3" fill="{CYAN}"/><path d="{micro}" fill="{NAVY}"/>'+microAccent)
# Quiet graphic language: the symbol's 2:1 diagonal, used once per composition.
save('graphic-ribbon.svg',1200,400,f'<path d="M0 0H220L1020 400H800Z" fill="{CYAN}"/>','samirhv diagonal graphic')
metadata={'name':'samirhv','edition':'S essencial / 02','revision':'subtle yellow terminal','status':'identity proposal, not deployed','palette':{'cyan':CYAN,'graphite':NAVY,'mist':WHITE,'muted':MUTED,'cyanInk':INK,'yellowAccent':YELLOW},'accent':{'path':ACCENT,'widthIn100UnitGrid':2,'usage':'Upper terminal only; omitted from monochrome signatures.'},'typeface':'Manrope','wordmarkWeight':750,'horizontalWidth':width,'wordWidth':wordWidth,'wordHeight':wordHeight,'symbolPath':SHAPE,'fontSource':'https://github.com/google/fonts/tree/main/ofl/manrope','fontSha256':hashlib.sha256((F/'Manrope.ttf').read_bytes()).hexdigest()}
(ROOT/'identity.json').write_text(json.dumps(metadata,indent=2)+'\n')
print('Outlined vectors generated:',len(list(A.glob('*.svg'))),'horizontal',width,'x 100')

def outlined_text(text, size, x, baseline, color, weight=700):
    f=TTFont(F/f'Manrope-{weight}.ttf');gs=f.getGlyphSet();cm=f.getBestCmap();sc=size/f['head'].unitsPerEm;cursor=x;paths=[]
    for ch in text:
        name=cm[ord(ch)];pen=SVGPathPen(gs);gs[name].draw(TransformPen(pen,(sc,0,0,-sc,cursor,baseline)));paths.append(pen.getCommands());cursor+=f['hmtx'][name][0]*sc
    return f'<path fill="{color}" d="'+ ' '.join(paths)+'"/>'
logoBody=mark(CYAN,True)+word(WHITE,120,(100-wordHeight)/2)
social=f'<rect width="1200" height="630" fill="{NAVY}"/><g transform="translate(66 54) scale(.6)">{logoBody}</g>'
social+=outlined_text('Engenharia',74,72,306,WHITE)+outlined_text('com assinatura.',74,72,399,WHITE)
social+=outlined_text('Projetos, ferramentas e código.',24,75,470,MUTED,400)
social+=f'<path d="M1010 0H1200V95L900 630H710Z" fill="{CYAN}" opacity=".08"/>'
social+=f'<rect x="72" y="556" width="48" height="4" fill="{CYAN}"/>'+outlined_text('samirhv.com.br',19,144,565,WHITE,500)
save('social-cover.svg',1200,630,social,'samirhv social cover concept')
repo=f'<rect width="1280" height="640" fill="{NAVY}"/><g transform="translate(76 170) scale(1.9)">{logoBody}</g>'
repo+=outlined_text('Projetos, ferramentas e código.',30,99,422,MUTED,400)+f'<path d="M1110 0H1160L840 640H790Z" fill="{CYAN}" opacity=".15"/>'
save('repository-cover.svg',1280,640,repo,'samirhv repository cover concept')
# Design tokens are portable reference values, not a production stylesheet.
(ROOT/'tokens.css').write_text(f''':root {{
  --brand-cyan: {CYAN};
  --brand-graphite: {NAVY};
  --brand-mist: {WHITE};
  --brand-muted: {MUTED};
  --brand-cyan-ink: {INK};
  --brand-yellow-accent: {YELLOW};
  --brand-font: "Manrope", Arial, sans-serif;
  --brand-radius-control: 8px;
  --brand-radius-panel: 16px;
  --brand-space-unit: 8px;
}}
''')
