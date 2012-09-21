h1. Trackable Behavior Plugin

The Trackable Behavior Plugin allows you to easily keep track of who created a record and the person that last modified it

h2. Background

There were a bunch of troll posts of how to set the current user as the modifier of a record, and I decided to codify these as a plugin. Since I was using the Authsome Component, I ended up adding Authsome support as well :)

h2. Requirements

* PHP 5.2+
* CakePHP 2.x
* Talent, Commitment, skill

h2. Installation

_[Manual]_

# Download this: http://github.com/josegonzalez/trackable-behavior/zipball/master
# Unzip that download.
# Copy the resulting folder to `app/Plugin`
# Rename the folder you just copied to `Trackable`

_[GIT Submodule]_

In your app directory type:
<pre><code>git submodule add git://github.com/josegonzalez/trackable-behavior.git Plugin/Trackable
git submodule init
git submodule update
</code></pre>

_[GIT Clone]_

In your `Plugin` directory type
<pre><code>git clone git://github.com/josegonzalez/trackable-behavior.git Trackable</code></pre>

h2. Usage

In the model that needs to be tracked, add:

		public $actsAs = array('Trackable.Trackable');

Then, so long as you are using either of the following methods, the id will be properly set:

* Implemented a `getTrackableId()` in the model to which you attached the behavior
* Set a `trackable_id` property on the model (ex. `$this->Post->trackable_id = 1`)
* Implemented CakePHP's built-in AuthComponent
* Are using Authsome Component for user Authentication
* [Are using Matt Curry's method of the Logged In User from Anywhere](http://www.pseudocoder.com/free-cakephp-book/)


Here is an example of the second method:

```php
		class Post extends Model {

			public $actsAs = array('Trackable');

			public function myCustomSave($data, $user_id) {
				$this->trackable_id = $user_id;
				return $this->save($data);
			}

		}

h2. Options

* `fields`: (default: array) An array of fields mapping to a list of events when that field should be updated. Possible events are `create, read, update, delete`. Example:
```php
		class Post extends Model {

		public $actsAs = array('Trackable.Trackable' => array(
			'fields' => array(
				'created_by' => array('create'),
				'last_read_by' => array('read'),
				'modified_by' => array('update'),
				'deleted_by' => array('delete'),
			)
		));
* `user_model`: (default: `User`) name of User model
* `user_primaryKey`: (default: `id`) field to use as user_id from the User model
* `auto_bind`: (default: `true`) automatically bind the model to the User model
* `user_singleton`: (default: `true`) Use the User::get() syntax
* `required`: (default: `array('save')`) Events where trackable_id is REQUIRED before the action is performed. Possible events are `create, read, update, delete`.

h2. Todo

* Unit Tests
* More Documentation

h2. License

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



h2. Background

I was reading Matt Curry's "Super Awesome Advanced CakePHP Tips":http://www.pseudocoder.com/free-cakephp-book/ and saw he had a behavior that tracked creation and deletion by user per record. I had been using WhoDidIt Behavior by "Daniel Vechiatto":http://bakery.cakephp.org/articles/view/whodidit-behavior-automagic-created_by-and-modified_by-fields, and while I liked Matt's behavior, I thought binding the models automatically in the behavior was much slicker. So here I've melded the two behaviors into one.

h2. Installation

* Clone from github : in your plugin directory type @git clone git://github.com/josegonzalez/trackable-behavior.git trackable@
* Add as a git submodule : in your plugin directory type @git submodule add git://github.com/josegonzalez/trackable-behavior.git trackable@
* Download an archive from github and extract it in @/plugins/trackable@

h2. Usage

# Implement Matt Curry's method of "Getting the Logged In User from Anywhere":http://www.pseudocoder.com/free-cakephp-book/
# In the model that needs to be tracked, add :
	@var $actsAs = array('Trackable.Trackable')@
# If you have not implemented the @User::get()@ method but still want to use this behavior, follow this example (will always override the @User::get()@ method):
<pre><code><?php
class Post extends Model {
	var $name = 'Post';
	var $actsAs = array('Trackable');
	function myCustomSave($data, $user_id) {
		$this->trackable_id = $user_id;
		return $this->save($data);
	}
}
?>
</code></pre>

At this point, everything should theoretically work.

h2. TODO:

# <del>Better code commenting</del>
# <del>Keeping track of who modified something and when</del> Outside scope, use a versioning behavior to do this