
<div id="divError">{ERROR}</div>

<form method="post" action="actualizar.php?id_encuentro={ADMINACTUALIZAR_IDENCUENTRO}" name="formRegistro">
<h2>{ADMINACTUALIZAR_RONDA}</h2>

<TABLE class="tableActualizar"><TR>
	<TD align="left">
		Contrincante 1:<br>
		<input type="text" readonly disabled value="{ADMINACTUALIZAR_E1}">
	</TD><TD align="left">
		Contrincante 2:<br>
		<input type="text" readonly disabled value="{ADMINACTUALIZAR_E2}">		
	</TD>
</TR><TR>
	<TD align="left">
		Resultado:
	</TD><TD align="left">
		<input name="encuentro[resultado]" type="text" maxlength="255" value="{ADMINACTUALIZAR_RESULTADO}">
		(hasta 255 caracteres, puedes incluir comentarios explicativos)
	</TD>
</TR><TR>
	<TD align="left">
		Ganador:
	</TD><TD align="left">
		<input name="encuentro[ganador]" type="radio" value="1" {ADMINACTUALIZAR_WIN1}>{ADMINACTUALIZAR_E1}<br>
		<input name="encuentro[ganador]" type="radio" value="2" {ADMINACTUALIZAR_WIN2}>{ADMINACTUALIZAR_E2}<br>
		<input name="encuentro[ganador]" type="radio" value="0" {ADMINACTUALIZAR_EMPATE}>Empate
	</TD>
</TR></TABLE>
	<input type="button" value="Enviar" class="submit" onClick="if (confirm('¿Enviar este resultado?')) submit();">
</form>