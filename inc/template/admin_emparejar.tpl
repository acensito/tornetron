
<div id="divError">{ERROR}</div>

<form method="post" action="emparejar.php?modo=submit" name="formRegistro">
<input name="id_juego" type="hidden" value="{EMPAREJAR_ID}">

<a href="../ver_clanes.php?id_juego={EMPAREJAR_ID}">Equipos inscritos: {EMPAREJAR_NEQUIPOS}</a>

<script language="JavaScript" type="text/javascript">	
	//MOSTRAR CAPA
	function showDiv(id)
	{			
		div = document.getElementById(id);		
		div.style.display="";
	}
	
	//OCULTAR CAPA
	function hideDiv(id)
	{			
		div = document.getElementById(id);		
		div.style.display="none";
	}
</script>

<TABLE><TR>
	<TD align="left">
		Tipo de competicion:
	</TD><TD align="left">
		<input name="tipo_torneo" type="radio" value="1" checked onClick="hideDiv('tdLyT')">Torneo eliminatorio<br>
		<input name="tipo_torneo" type="radio" value="2" onClick="hideDiv('tdLyT')">Liga<br>
		<input name="tipo_torneo" type="radio" value="3" onClick="showDiv('tdLyT')">Liguillas + Torneo eliminatorio<br>
		<input name="tipo_torneo" type="radio" value="4" onClick="hideDiv('tdLyT')">Brackets Winners/Losers<br>
	</TD>				
	<TD align="left" style="display:none" id="tdLyT">
		Número de grupos:{EMPAREJAR_NGRUPOS}
		
		<br>Equipos clasificados en cada grupo:
		<input name="nclasif" type="radio" value="1">1
		<input name="nclasif" type="radio" value="2" checked>2
	</TD>
</TR></TABLE>

	<input type="submit" value="Enviar" class="submit">
</form>