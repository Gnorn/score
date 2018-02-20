# score
Keep a trace of a group of players' games results, calculate ranking according to the Tenhou system (ELO-based)

Copyright 2016 Simon Picard

> This program is free software: you can redistribute it and/or modify
> it under the terms of the GNU General Public License as published by
> the Free Software Foundation, either version 3 of the License, or
> (at your option) any later version.
>
> This program is distributed in the hope that it will be useful,
> but WITHOUT ANY WARRANTY; without even the implied warranty of
> MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
> GNU General Public License for more details.
>
> You should have received a copy of the GNU General Public License
> along with this program.  If not, see <http://www.gnu.org/licenses/>.


## Requirements

PHP5, MySql, jpgraph 4.1.0, arial font file

## Installation

This is a web based application. You can run it on a web server (local or distant) with PHP5 and MySql.

* secure access to the folder on your server (I use a .htaccess)
* copy the files and add the dependencies (jpgraph and the arial font file)
* rename connect.php.sample to connect.php and edit with your MySql login details
* create the following MySql tables : Ligue_Joueurs , Ligue_Parties , Ligue_Score (for example with PhpMyAdmin or similar)
* insert players into the Ligue_Joueurs table

## Security disclaimer

I'm not a professionnal developper, use at your own risks!

## How to use

Insert games through index.php (see instructions bellow for optional CSV filling).

The following file will interpret the results in the form of HTML tables, you can include them somewhere else on your website, add CSS, etc. :
* classement.php : general ranking page
* partie.php : info about a particular game (accessed through classement.php)
* joueur.php : info about a particular player (accessed through joueur.php)

## CSV filling

For convenience you can bulk add a lot of games at once (say, the results of a tournament) through a CSV formatted bulk of text. Each game is described as follow:

>Player1, Score1
>Player2, Score2
>Player3, Score3
>Player4, Score4

It should be pretty self-explanatory. If you add more lines they won't be interpreted, so you can use that space for comments or whatever you like

Games are separated by a line consisting of the following sequence : « --- »

For example :

> Chantal, -3
> Élise, -35
> Seiko, 24.2
> Guillaume, 13.8
> ---
> Seiko, -22.3
> Élise, -35
> Guillaume, 43.9
> Chantal, 13.4

will insert two games, in the first Chantal scored -3, Élise -35, Seiko +24.2 and Guillaume 13.8, and in the second Seiko scored -22.3, Élise -35, Guillaume 43.9 and Chantal 13.4
