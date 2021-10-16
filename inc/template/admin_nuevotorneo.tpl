
<div id="divError">{ERROR}</div>

<form method="post" action="nuevotorneo.php?modo=submit" name="formRegistro">
<h2>Nuevo torneo</h2>

<TABLE><TR>
	<TD align="left">
		Nombre del torneo:
	</TD><TD align="left">
		<input name="torneo[nombre]" type="text" maxlength="255" value="{NUEVOTORNEO_NOMBRE}">
		(*)
	</TD>
</TR><TR>
	<TD align="left">
		Mínimo de jugadores por equipo (ej: 1 para torneos individuales):
	</TD><TD align="left">
		<input name="torneo[numero_jugadores]" type="text" maxlength="9" value="{NUEVOTORNEO_NJUG}">
		(*)
	</TD>
</TR><TR>
<TD align="left">
		Número máximo de suplentes:
	</TD><TD align="left">
		<input name="torneo[numero_suplentes]" type="text" maxlength="9" value="{NUEVOTORNEO_NSUP}">
		(*)
	</TD>
</TR><TR>	
	<TD align="left" colspan="2">
		(*) Campos obligatorios
	</TD>
</TR></TABLE>

	<input type="submit" value="Enviar" class="submit">
</form>