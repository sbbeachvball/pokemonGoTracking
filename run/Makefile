all: help

clean:
	(cd ..; find . -type f -name '*~' -exec rm {} \;)
	
init:
	(cd ../utils; rm ../data/pokemon.sqlite; ./buildPokeDex.php --drop-tables=pokemon,user,candy,has --create-tables=pokemon,user,candy,has --load-tables=pokemon)

clean-db:
	(cd ../utils; rm ../data/pokemon.sqlite; ./buildPokeDex.php --drop-tables=pokemon --create-tables=pokemon --load-tables=pokemon)

bk:
	backup
    
backup:
	cp ../data/pokemon.sqlite ../backups/pokemon.sqlite-`date +'%Y%m%d'`
	
help:
	@echo "Help system for pokemon evolve tracking system"
	@echo "make init     - runs a script that builds out a database from a template"
	@echo "                should not be run once using the system."
	@echo "make clean-db - runs a script that rebuilds the pokemon db (to fix bad input data)"
	@echo "make clean    - removes jEdit temp files"
	@echo "make bk       - create a date stamped backup"
	@echo "make backup   - create a date stamped backup"
