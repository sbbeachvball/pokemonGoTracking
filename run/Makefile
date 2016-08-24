all: help

clean:
	(cd ..; find . -type f -name '*~' -exec rm {} \;)
	
db:
	(cd ../utils; rm ../data/pokemon.sqlite; ./buildPokeDex.php)

backup:
	cp ../data/pokemon.sqlite ../backups/pokemon.sqlite-`date +'%Y%m%d'`
	
help:
	@echo "Help system for pokemon evolve tracking system"
	@echo "make db - runs a script that builds out a the db from a template"
	@echo "          should not be run once using the system."