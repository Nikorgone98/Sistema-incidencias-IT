
#!/usr/bin/python3

import csv
import subprocess

ruta = "/tmp/incidencia.csv"


query = """
SELECT
	i.id_incidencia,
	i.titulo,
	i.fecha_creacion,
	u.nombre,
	e.nombre_estado,
	p.nombre_prioridad
FROM incidencias i
INNER JOIN usuarios u
	ON i.id_usuario = u.id_usuario
INNER JOIN estados e
	ON i.id_estado = e.id_estado
INNER JOIN prioridades p
	ON i.id_prioridad = p.id_prioridad
ORDER BY i.id_incidencia;
"""

comando = [
	"mysql",
	"-u","turingadmin",
	"-pTuringAsir2026*",
	"-D","turinghelpdesk",
	"--batch",
	"--raw",
	"-e", query
	]


resultado = subprocess.run(
	comando,
	capture_output=True,
	text=True,
	check=True
	)

lineas = resultado.stdout.strip().split("\n")

with open(ruta, "w", newline="", encoding="utf-8") as archivo_csv:

    escritor = csv.writer(archivo_csv)

    for linea in lineas:
        escritor.writerow(linea.split("\t"))

print(ruta)
