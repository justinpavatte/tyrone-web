<?php
if($_SERVER['REQUEST_METHOD']=='POST'){
header('Content-Type:application/json');
$j=json_decode(file_get_contents('php://input'),1);
$p="You are Tyrone, an African American man living in Detroit. You speak in a natural Ebonics/AAVE style. Keep answers useful, clear, and conversational. Continue the conversation.\n\n";
foreach(($j['m']??[]) as $x)$p.=($x[0]=='a'?'Tyrone: ':'User: ').$x[1]."\n\n";
$p.="Tyrone:";
$c=curl_init('http://100.116.188.35:11434/api/generate');
curl_setopt_array($c,[CURLOPT_RETURNTRANSFER=>1,CURLOPT_POST=>1,CURLOPT_HTTPHEADER=>['Content-Type:application/json'],CURLOPT_POSTFIELDS=>json_encode(['model'=>'llama3:latest','prompt'=>$p,'stream'=>false]),CURLOPT_TIMEOUT=>120]);
$r=curl_exec($c);
echo $r?:json_encode(['error'=>curl_error($c)]);
exit;
}
?>
<!doctype html>
<html>
<head>
<meta name=viewport content="width=device-width,initial-scale=1">
<title>Tyrone</title>
<style>
*{box-sizing:border-box}
body{margin:0;background:#0f1115;color:#f2f2f2;font-family:system-ui,Arial,sans-serif}
#intro{position:fixed;top:0;left:0;right:0;background:#151922;border-bottom:1px solid #2a2f3a;padding:18px;text-align:center;font-size:22px;font-weight:600;z-index:2}
#o{max-width:850px;margin:0 auto;padding:90px 14px 95px;white-space:pre-wrap;line-height:1.45}
.msg{margin:0 0 14px;padding:12px 14px;border-radius:14px}
.u{background:#23324a}
.a{background:#1b1f2a}
#b{position:fixed;bottom:0;left:0;right:0;background:#151922;border-top:1px solid #2a2f3a;padding:12px;display:flex;gap:8px}
#p{flex:1;min-width:0;background:#0f1115;color:white;border:1px solid #3a4050;border-radius:12px;padding:13px;font-size:16px}
button{background:#3d6cff;color:white;border:0;border-radius:12px;padding:0 18px;font-size:16px;white-space:nowrap}
button:disabled{opacity:.5}
</style>
</head>
<body>
<div id=intro>Yo, im Tyrone. Wat u need help wit?</div>
<div id=o></div>
<div id=b><input id=p autofocus autocomplete=off><button id=btn onclick=s()>Send</button></div>
<script>
m=[];
function add(t,c){d=document.createElement('div');d.className='msg '+t;d.textContent=c;o.appendChild(d);scrollTo(0,document.body.scrollHeight);return d}
async function s(){
v=p.value.trim();if(!v||btn.disabled)return;
m.push(['u',v]);add('u','You: '+v);p.value='';btn.disabled=1;
d=add('a','Tyrone: thinking...');
try{
r=await fetch('',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({m})});
j=await r.json();a=j.response||JSON.stringify(j,null,2);
d.textContent='Tyrone: '+a;m.push(['a',a]);
}catch(e){d.textContent='Tyrone: '+e}
btn.disabled=0;p.blur();scrollTo(0,document.body.scrollHeight);
}
p.onkeydown=e=>{if(e.key=='Enter'){e.preventDefault();s()}}
</script>
</body>
</html>
