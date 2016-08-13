# pokemonGoTracking
Playing around with the idea of building a personal pokemon GO evolution tracking guide.
I like to accumulate my evolutions and then do an "evolve party", using a luck egg and
doing all of them at once.  

Currently tracking evolves in a google docs sheet.  Sort by number of evolves and then 
print out before going on the "evolve party".

Thinking it could be cool to have a database with a table of the pokemon and attributes.
Provide some html to add pokemon to db as we find them.
Then have a table for tracking users candy.
Have a table of the pokemon types as well, provide a select pulldown to set some values.

## Features/Specs
* Ability to add pokemon as we discover new ones
* Ability to update pokemon specs
* Ability to maximize XP (number of evolves) OR maximize CP (trying to get pokemon evolved to max)
* Ability to display table of a given users pokemon counts and candy
** Ability to increment/decrement the counts and candy
* Ability to produce a compact report of the evolves to perform.

## Tables?
* pokemon   - pID int primary key not null, pName text, pBase text, pTypeID int, evLevel int default 0, evTo text, evCost int default 0
** pID should correspond to number in pokedex...
* user      - userID int primary key not auto increment, userName text;
* poketypes - pTypeID int primary key auto increment, pTypeName text, (Battle Data - effective against...) 
* 

## Technologies?
* Thinking of using node/express/sass
** sqlite module?
* Firebase? might be interesting use case if I can figure out how to use it :-)
** Would love to have data bound directly into the db so I didn't have to do all that work
* userData  - userID int primary key auto increment, pID int, pCount int, cCount int
