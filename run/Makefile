all: clean

clean:
	(cd ..; find . -type f -name '*~' -exec rm {} \;)
	
db:
	(cd ../utils; ./buildPokeDex.php)

