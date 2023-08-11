<html>
	<center>
		<style type="text/css">
			#header
			{
			height:80px;
			width:700px;
			background-color:#00966d;
			border-radius:0 25px 0 0;
			}
			
			#body
			{
			width:700px;
			height:350px;
			background-color:#ffffff;
			}
			
			#div1
			{
			width:700px;
			height:5px;
			}
			
			
			#footer
			{
			width:700px;	
			height:60px;
			background-color:#00966d;
			border-radius:0 0 0 25px;
			}
			
			#linea
			{
			width:700px;
			height:50px;
			background:#ffe707;
			}
			
			#blanco
			{
			width:700px;
			height:45px;
			background-color:#ffffff;
			border-radius: 0 0 100% 0;
			}
			
			#blanco2
			{
			width:700px;
			height:45px;
			background-color:#ffffff;
			position: relative;
			border-radius: 80% 0 0 0;
			padding: 10px 0 0 0;
			}
            .btn{
                background-color:#00966d;
                padding: 10px 15px;
                border-radius: 5px;
                color:white;
                font-size: 18px;
            }
		</style>
		<body>
			<div id="header">
				<img src="<?= Yii::getAlias('@web'); ?>/images/tms.png" align="left" style="padding: 10px;">
			</div>
			<div id="linea">
				<div id="div1">
				</div>
				<div id="blanco2">
				</div>
			</div>
			<div id="body" style="font-family:Verdana;">
				<p align="center"><h1>Tracking de Servicios</h1></p>
				<p align="left">Estimado Cliente/a:</p>
				<p align="left">Junto con saludarle muy cordialmente me es grato dirigirme a Ud. con el fin de informale que hay un servicio para usted</a></p>
				<table border="1" style="border-collapse: collapse;">
				    <tr>
				        <td style="padding:3px;">CLIENTE</td>
				        <td style="padding:3px;">@cliente</td>
				    </tr>
				    <tr>
				        <td style="padding:3px;">TRANSPORTISTA</td>
				        <td style="padding:3px;">@transportista</td>
                    </tr>
                    <tr>
				        <td style="padding:3px;">PATENTE</td>
				        <td style="padding:3px;">@patenteInfo</td>
				    </tr>
				    <tr>
				        <td style="padding:3px;">CHOFER</td>
				        <td style="padding:3px;">@chofer</td>
				    </tr>
				 </table>
			</div>
            <div class="boton">
                <a href="" class="btn">Ver viaje</a>
            </div>
			<div id="linea">
				<div id="blanco">
				</div>
			</div>
			<div id="footer" style="padding:40px 0 0 0;" >
				<font color="#ffffff">&copy; 2019 BermannTMS, inc. Todos los derechos reservados.</font>
			</div>
		</body>
	</center>
</html>