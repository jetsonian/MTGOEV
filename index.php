<html>
	<head>
		<title>MTGOEV.com</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<script>
		function change_text ( input )
		{
			document.getElementById("Text").innerHTML = input;
		}
		</script>
	</head>
	<body>
		<h1>Draft</h1>
		<div class="draft">
			<img src="./images/FRF_Symbol.png" alt="KTK Symbol" class="symbol"  onclick="change_text("Image");"/>
			<div class="ev_box">
				<div class="line_box">
					<div class="format">FRF-KTK-KTK 8-4:</div>
					<div class="ev">-3.40 tix</div>
				</div><br/>
				<div class="line_box">
					<div class="format">FRF-KTK-KTK 4-3-2-2:</div>
					<div class="ev">-3.40 tix</div>
				</div><br/>
				<div class="line_box">
					<div class="format">FRF-KTK-KTK Swiss:</div>
					<div class="ev">-3.40 tix</div>
				</div>
			</div>
		</div>

		<form>
		<div class="button" onclick="change_text("1");">
	     	<input type="radio" name="source" id="Owned" checked>
    	    <label for="Owned" unselectable>Owned</label>
	    </div>
    	<div class="button" onclick="change_text("2");">
        	<input type="radio" name="source" id="Bots">
	        <label for="Bots" unselectable>Bots</label>
    	</div>
    	<div class="button" onclick="change_text("3");">
	        <input type="radio" name="source" id="WOTC">
    	    <label for="WOTC" unselectable>WOTC</label>
	    </div>
		</form>

		<div id="Text">0</div>

	</body>
</html>