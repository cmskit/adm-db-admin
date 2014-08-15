# Database management

**Warning:** To use database management you should have knowledge about databases and sql.
You could really mess up your database (and with it the whole installation) with a single statement!

## Adminer interface

This wizard is using [Adminer](http://www.adminer.org), a database administration tool.
Help can be found at <http://www.adminer.org/en/>.


### Updating adminer interface

To update adminer itself just download the new adminer-x.y.z.php (the full version) from <http://www.adminer.org/en/#download>,
rename it to adminer.inc (not adminer.php!!) and replace the old one with the new one.

To enhance or customize the program 

* plugin.php is a collection of adminer plugins found at <http://www.adminer.org/en/plugins>
* adminer.css is a customized skin taken from <http://www.adminer.org/en/#extras>


## Import dada

* to import data into your database you can of course use adminer itself (import sql dumps). 
* to import xml-encoded data there is a simple import dialog found at the entrance page.
