<?php $out = <<<HTML
<!doctype html>
<html lang="en">
<head>
	<title>Lat CMS</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADoAAAAyCAYAAAAN6MhFAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA3dJREFUeNrkmk1u2kAYhm2aTVdln0pxTlBYRF1iTlDSC9RsKlWqBOy6w+y6g0iVKnWDe4GGnsDOssoizg3cRbONb9B+g16qifHM+I8B7E+ykKyYmcfv9zvkmaHZLi5eO6enL326nj88/Al0rXuiGdKmj6WxB2tpXm9k7MlaGtVs08eg9qBktrFH0wnaaQroq6aAtpsCKrT3n7/6dDlNSEbr+kqw41orytmUYNtNAGWQThNAmfWaAmofLSi1f1Ye991FnOpS1Mr5951jBU1zx7CODUNa+xfr7KJ0gQ5yKnp8rsuOTlJuR7e3v+K6ue67lHtBrZp6lJW0RPSzbtPLNC0JkduuFM+9OBpQnPilxadXtzo6F9y/ylBqjgOU1HQFm12Q20YZSg1rA62DBiXIgSg26ZoJHot23dy3KoZkKopO4oeS2nmfcu/NQYIC0he0b54i06bV1UGV7tvSAMnibyJ7/tunD6HAfZ2DASVIdqB1J4HsZ2z30hQfVTWbtkoAWnT5kjKSB5LZd8EUU8nJoFmifIwk4xSLucu8jTs73xVk23Ny76gM6ElOQBsKygr6jADdgvuZCUBZJu9rcV2o6EsgN65aFJIlpUAQqzap7e7UdfG75rWkgDP3nBCgV0UsoaSIktuQXoZXuaKA9CWQCxY/VUFC1UhSjpZFf6MxM0B2BMlmKOhbjYqUXUrqaG5lTUVMTlN6Ugb4pJP5/eNjG6r38GI6AteLEcsh2r7V2dsvsQT2TpITZgTrVgH6yG02RjZdJAAd9KRl/jchQA3dgkazIEuAAdSNyoD+5QblCV8TCdDFWVCVo1SMWXXBAwN2LnHj9XMqdWWg11Ax5AAHWNQydmfrLE6wXkJdVzD+PQkrlKhinREBdgBoG/qMbXhGwAEHuxkDZQ3LgmAnuUCRZOYZp4gQm7thqvAb5L7PRtx3ENtZjkw8AEcc8BjqtiV7ueRj15RAqr5s84VXquypeJEDxLutil9aw80Zu32MgNugeOtLRRxuuVVZw7pTBXCE+F0l3FkUVv9hTW4hC4CqhYZVAhYEDgAccsCiRBkSaNeE+0wVc18EBT1dmQg1eqSIYw/AcSI7J0fIYQs+PpZM/UzBc52QzNh6dHUxnonWdpIlB/W0m3jGMrm314PsN1CwUILZocKbNpMpfIb4u5cJQOpuwuDmnwADADHZZXUSqN+uAAAAAElFTkSuQmCC" />
	<link rel="stylesheet/less" href="/style.less">
	<script src="/js/less.js"></script>
	<!-- IE not supported!! -->
	<!--[if lt IE9]><script>alert('go home and install chrome, i dont support filthy ie');window.location='https://www.google.com/chrome';</script><![endif]-->
</head>
<header>
<nav id="nav-primary">
<ul>
<li><a href="#">Navigational Item</a></li>
<li><a href="#">Navigational Item</a></li>
<li><a href="#">Navigational Item</a></li>
<li><a href="#">Navigational Item</a></li>
<li><a href="#">Navigational Item</a></li>
<li><a href="#">Navigational Item</a></li>
<li><a href="#">Navigational Item</a></li>
<li id="account">guest</li>
</ul>
</nav>
<section>
<h1><a href="/">Mikelat's Site</a></h1>
<aside>
<nav id="nav-secondary">
<ul>
<li><a href="#">Navigational Item</a></li>
<li><a href="#">Navigational Item</a></li>
<li><a href="#">Navigational Item</a></li>
<li><a href="#">Navigational Item</a></li>
<li><a href="#">Navigational Item</a></li>
<li><a href="#">Navigational Item</a></li>
<li><a href="#">Navigational Item</a></li>
</ul>
</nav>
<form action="/" method="post" id="search" class="close">
<input type="text" placeholder="enter search terms" />
<input type="hidden" name="section" id="search-section" value="" />
<button id="search-button" type="submit"><span class="icon search"></span>search</button>
</form>
<form action="/" method="post" id="login">
<input type="text" placeholder="username" tabindex="1" /><input type="password" placeholder="password" tabindex="2" />
<button id="login-button" type="submit" tabindex="4"><span class="icon user"></span>login</button>
<div>
<a href="#">Register</a>
<a href="#">Recover Password</a>
<label><input type="checkbox" value="1" tabindex="3" /> remember</label>
</div>
</form>
</aside>
</section>
</header>
<section id="content">
<!-- var:html -->
</section>
<div id="loading"></div>
<script src="/js/jquery.js"></script>
<script src="/js/init.js"></script>
HTML;

//<script>var base_url='/', lat=<?php echo json_encode($js_vars);

/*
echo script_tag(base_url('js/jquery.js')).script_tag(base_url('js/init.js')).script_tag('http://www.google.com/recaptcha/api/js/recaptcha_ajax.js');
foreach($js_files as $j):
echo script_tag($j);
endforeach;
echo nl();
if($js_code):
echo "<script>".implode("\n", $js_code)."</script>";
endif;

echo nl(); ?>
</body>
</html>*/