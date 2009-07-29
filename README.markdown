The Trackable Behavior Plugin allows you to easily keep track of who created a record and the person that last modified it

## Background
I was reading Matt Curry's Super Awesome Advanced CakePHP Tips (http://www.pseudocoder.com/free-cakephp-book/) and saw he had a behavior that tracked creation and deletion by user per record. I had been using WhoDidIt Behavior by Daniel Vechiatto (http://bakery.cakephp.org/articles/view/whodidit-behavior-automagic-created_by-and-modified_by-fields), and while I liked Matt's behavior, I thought binding the models automatically in the behavior was much slicker. So here I've melded the two behaviors into one.

## Installation
- Clone from github : in your plugin directory type `git clone git://github.com/josegonzalez/trackable-behavior.git trackable`
- Add as a git submodule : in your plugin directory type `git submodule add git://github.com/josegonzalez/trackable-behavior.git trackable`
- Download an archive from github and extract it in `/plugins/trackable`

## Usage
1. Implement Matt Curry's method of 'Getting the Logged In User from Anywhere' (http://www.pseudocoder.com/free-cakephp-book/)
2. In the model that needs to be tracked, add :
	var $actsAs = array('Trackable.Trackable')

At this point, everything should theoretically work.

## TODO:
1. Better code commenting
2. Keeping track of who modified something and when