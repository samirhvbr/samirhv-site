"""Build a single-page overview using the same outlined artwork."""
from pathlib import Path
import runpy
b=runpy.run_path(str(Path(__file__).with_name('build-vectors.py')))
R=b['ROOT'];A=b['A'];N=b['NAVY'];C=b['CYAN'];F=b['WHITE'];M=b['MUTED'];t=b['outlined_text']
import xml.etree.ElementTree as ET

def asset(name,x,y,w):
 root=ET.parse(A/name).getroot();scale=w/float(root.attrib['width']);body=''.join(ET.tostring(ch,encoding='unicode') for ch in root)
 return f'<g transform="translate({x} {y}) scale({scale})">{body}</g>'
s=f'<rect width="1600" height="1220" fill="{N}"/>'
s+=t('samirhv / identidade visual',20,80,73,C,500)+t('S essencial / 02',20,1310,73,M,400)
s+=asset('logo-horizontal-dark.svg',160,148,1280)
s+=t('Engenharia com assinatura.',30,192,510,F,500)
s+=f'<rect x="80" y="588" width="915" height="258" rx="16" fill="{F}"/>'
s+=asset('logo-horizontal-light.svg',118,640,830)+asset('app-icon-cyan.svg',1070,588,258)
s+=asset('favicon.svg',1388,654,64)+t('Favicon',17,1387,765,M,400)
s+=t('Manrope',50,80,960,F,700)+t('Preciso. Autoral. Direto.',22,82,1015,M,400)
s+=t('Detalhe na ponta / #FFFF00',16,82,1068,M,400)
for j,(name,h) in enumerate([('Ciano',C),('Grafite',N),('Névoa',F),('Ciano texto',b['INK'])]):
 x=650+j*221;s+=f'<rect x="{x}" y="911" width="195" height="94" rx="6" fill="{h}" stroke="#334050"/>'
 s+=t(name,17,x,1040,F,500)+t(h,16,x,1071,M,400)
s+=t('Samir Hanna Verza',17,80,1155,M,400)+t('Proposta de identidade / setembro 2026',17,1140,1155,M,400)
(R/'overview.svg').write_text(b['svg'](1600,1220,s,'samirhv identity overview'))
