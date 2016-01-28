# [Prjct](http://prjct.work/)

With prjct you show easily your projects on the web ([Demo](http://work.jonaszielke.de)). You don't need a database or any programming experience. Just upload this [zip file](https://github.com/jonaszielke/prjct/archive/master.zip) via FTP to your server and add your projects into `/projects/`. That's it!

## Requirements
* Apache server (no SunOS, sorry) and PHP enabled
* A FTP account for uploading files
* Your brain and a nice image of you :)

## Structure of a project
0. Add a new folder. Every project folder needs a unique number (for the correct order) and a unique name, like `12 Hello World`.
0. Do you want to split your project into separate sub projects?
 * :+1: Yes: Create new folders under the main project folder - according to step #1. Every sub project needs one Markdown file and at least one image. The last sub project will be displayed on the projects overview.
 * :-1: No: When you don't use sub projects, a Markdown file and image in the root of the project is required.
0. In order to provide files to download, create a folder named `attach` in the project root or sub project. They will be shown as a list under the description.

## Setup your profile
* You don't want my image on your site? It's ok :) Replace the file `/assets/image/profileimage.jpg` with yours.
* Open `/assets/projects/info.md` and edit the Markdown entries to your needs. Please don't use a dash after a new line.

## Tips
* Don't miss the .htaccess!
* Give every project, sub project and image a unique number. With this you have everything in the right order.
* The project images have a maximum width of 500px - for retina displays 1,000px. Height doesn't matter ;)
* When you use sub projects and you need a general description of your project. Just add a Markdown file and images to the root project folder. This will be displayed on the top of the project site (and not on the projects overview).

## ToDo
- [ ] Pagination
- [ ] Password protection
- [ ] Play audio and video inline

## Support
* What is Markdown? Click [here](https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet).
* Did you find some bugs? Add an [issue](https://github.com/jonaszielke/prjct/issues/new) or pull a [request](https://github.com/jonaszielke/prjct/compare)!
* Do you need help? Try it with a [Tweet](https://twitter.com/intent/tweet?text=@prjctsdotwork%20).


## Release History
* 1.0.0 Initial commit :tada:
    * 1.0.1 small CSS bug fixes
    * 1.0.2 Markdown support for profile section, Update [README](https://github.com/jonaszielke/prjct/blob/master/README.md)
    * 1.0.3 Better position for URLs in profile section

## License
Copyright © 2016 [Jonas Zielke](http://www.jonaszielke.de) and released under [MIT](https://github.com/jonaszielke/prjct/blob/master/LICENSE.txt) license.

# Third party libraries
* [Parsedown](https://github.com/erusev/parsedown)
* [jQuery](https://github.com/jquery/jquery)
* [swiftclick](https://github.com/munkychop/swiftclick)
