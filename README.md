# pokemonGoTracking
Looking to build a personal pokemon GO evolution tracking guide, just playing around

Currently tracking evolves in a google docs sheet.

Thinking it could be cool to have a database with a table of the pokemon and attributes.
Provide some html to add pokemon to db as we find them.
Have a table of the pokemon types as well, provide a select pulldown to set that value.

Then have a table for tracking users candy.
Ideally display users data almost as a spreadsheet but with controls to increment/decrement 
pokemon candy (and counts of pokemon)

table: pokemon   - pID int primary key not null, pName text, pBase text, pTypeID int, evLevel int default 0, evTo text, evCost int default 0
pID should correspond to number in pokedex...
table: user      - userID int primary key not auto increment, userName text;
table: poketypes - pTypeID int primary key auto increment, pTypeName text, (Battle Data - effective against...) 
table: userData  - userID int primary key auto increment, pID int, pCount int, cCount int
