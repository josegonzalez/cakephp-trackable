[![Build Status](https://travis-ci.org/josegonzalez/cakephp-trackable.png?branch=master)](https://travis-ci.org/josegonzalez/cakephp-trackable) [![Coverage Status](https://coveralls.io/repos/josegonzalez/cakephp-trackable/badge.png?branch=master)](https://coveralls.io/r/josegonzalez/cakephp-trackable?branch=master) [![Total Downloads](https://poser.pugx.org/josegonzalez/cakephp-trackable/d/total.png)](https://packagist.org/packages/josegonzalez/cakephp-trackable) [![Latest Stable Version](https://poser.pugx.org/josegonzalez/cakephp-trackable/v/stable.png)](https://packagist.org/packages/josegonzalez/cakephp-trackable)

# Trackable Behavior Plugin

The Trackable Behavior Plugin allows you to easily keep track of who created a record and the person that last modified it

## Background

There were a bunch of troll posts of how to set the current user as the modifier of a record, and I decided to codify these as a plugin. Since I was using the Authsome Component, I ended up adding Authsome support as well :)

## Requirements

* PHP 5.2+
* CakePHP 2.x
* Talent, Commitment, Skill

## Installation

_[Using [Composer](http://getcomposer.org/)]_

Add the plugin to your project's `composer.json` - something like this:

```composer
  {
    "require": {
      "josegonzalez/cakephp-trackable": "dev-master"
    }
  }
```

Because this plugin has the type `cakephp-plugin` set in its own `composer.json`, Composer will install it inside your `/Plugins` directory, rather than in the usual vendors file. It is recommended that you add `/Plugins/Trackable` to your .gitignore file. (Why? [read this](http://getcomposer.org/doc/faqs/should-i-commit-the-dependencies-in-my-vendor-directory.md).)


_[Manual]_

* Download this: http://github.com/josegonzalez/trackable-behavior/zipball/master
* Unzip that download.
* Copy the resulting folder to `app/Plugin`
* Rename the folder you just copied to `Trackable`

_[GIT Submodule]_

In your app directory type:

		git submodule add git://github.com/josegonzalez/trackable-behavior.git Plugin/Trackable
		git submodule init
		git submodule update

_[GIT Clone]_

In your `Plugin` directory type

		git clone git://github.com/josegonzalez/trackable-behavior.git Trackable

### Enable plugin

In 2.0 you need to enable the plugin your `app/Config/bootstrap.php` file:

		CakePlugin::load('Upload');

If you are already using `CakePlugin::loadAll();`, then this is not necessary.

## Usage

In the model that needs to be tracked, add:

		public $actsAs = array('Trackable.Trackable');

Then, so long as you are using either of the following methods, the id will be properly set:

* Implemented a `getTrackableId()` in the model to which you attached the behavior
* Set a `trackable_id` property on the model (ex. `$this->Post->trackable_id = 1`)
* Implemented CakePHP's built-in AuthComponent
* Are using Authsome Component for user Authentication
* [Are using Matt Curry's method of the Logged In User from Anywhere](http://www.pseudocoder.com/free-cakephp-book/)


Here is an example of the second method:

		class Post extends Model {

			public $actsAs = array('Trackable');

			public function myCustomSave($data, $user_id) {
				$this->trackable_id = $user_id;
				return $this->save($data);
			}

		}

## Options

* `fields`: (default: array) An array of fields mapping to a list of events when that field should be updated. Possible events are `create, read, update, delete`. Example:

		class Post extends Model {

			public $actsAs = array('Trackable.Trackable' => array(
				'fields' => array(
					'created_by' => array('create'),
					'last_read_by' => array('read'),
					'modified_by' => array('update'),
					'deleted_by' => array('delete'),
				)
			));
		}
* `user_model`: (default: `User`) name of User model
* `user_primaryKey`: (default: `id`) field to use as user_id from the User model
* `auto_bind`: (default: `true`) automatically bind the model to the User model
* `user_singleton`: (default: `true`) Use the User::get() syntax
* `required`: (default: `array('save')`) Events where trackable_id is REQUIRED before the action is performed. Possible events are `create, read, update, delete`.

## Todo

* Unit Tests
* More Documentation

## License

Copyright (c) 2009 Jose Diaz-Gonzalez

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
