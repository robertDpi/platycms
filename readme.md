# Boilerplatypus

Boilerplatypus is a crossbreed of [Stacey App](https://github.com/kolber/stacey) and [HTML5 Boilerplate](http://h5bp.com) with [Sass](http://sass-lang.com) and [CoffeeScript](http://coffeescript.org) flavor. Ladies and gentlemen, meet [the one, the only](http://www.youtube.com/watch?v=z8f2mW1GFSI): the *Boilerplatypus*.

[Why is Hitler?](http://en.wikiquote.org/wiki/Catch-22) — you may ask. What you see here is just my custom boilerplate for developing small websites, that's all.

## Stacey 3.0.0

Stacey is a simple and lightweight CMS. With Stacey you manage content by creating folders that contain very simple YAML files (if it sounds too complicated, be aware that YAML file is almost as dumb as regular .txt file). No need to set up database which your granma doesn't use anyway.

Stacey requires PHP >= 5.2 and mod_rewrite for clean URLs.

From version 3.0.0 Stacey uses [Twig templating language](http://twig.sensiolabs.org/).

Note that Boilerplatypus is slightly more advanced version of Stacey, so you have two options:

## Blue pill

If you just want your website up and running in few minutes, consider downloading [Stacey](https://github.com/kolber/stacey): it has 3 very nice default templates and you won't need to tinker with anything. It works right from the box! By the way, [Stacey 2.3.0](http://staceyapp.com) is even quicker and simpler.

## Red pill

If you want to stay on the bleeding-edge of web technologies and customize your website thoroughly, then Boilerplatypus is what you need.

Open a shell of your choice and run the following commands (let me assume that you use Mac, store your projects in `/Users/<your-username>/Sites/`, and already have created git repo named `ahoy-world`).

	cd ~/Sites/
	git clone git://github.com/kvakes/boilerplatypus.git
	mkdir ahoy-world
	cd ahoy-world/
	git init
	cp -r ../boilerplatypus/* .
	chmod 777 app/_cache/
	git add .
	git commit -m "Boilerplatypus"
	git remote add origin git@github.com:username/ahoy-world.git
	git push origin master

Now you have your project in `~/Sites/ahoy-world/` and you're ready to go. Just download and install [MAMP](http://www.mamp.info/en/index.html) (or install Apache and PHP yourself). Then change Apache's document root to `/Users/<your-username>/Sites/ahoy-world/`.

But — and it's a very big but! — if you want to harness the full power of Sass and CoffeeScript and other stuff, keep reading.

Assuming you have ruby and [rubygems](http://rubygems.org/pages/download) installed, let's proceed with Sass and CoffeeScript installation, we'll need it later.

	gem install sass coffee-script

### HTML5

Boilerplatypus brings you fully [H5BP](http://h5bp.com)ed template, stuffed with the custom Sassed CSS framework. Also, I emptied `content/` and `templates/` folders, so I don't do this manually every time I start new Stacey project.

### CSS

The idea behind Boilerplatypus' CSS framework is to download as few CSS files as possible without using any polyfills. Pursuing this goal we'll encounter 3 main problems:

1. desktop and mobile browsers download CSS even if media query doesn't match
2. IE6/7/8 do not support media-queries at all
3. mobile and desktop stylesheets often contain identical CSS rules (fonts, colors, decoration)

Sadly, all modern browsers download all CSS assets. It makes sense, when you try to achieve infamous responsiveish effect with `min-width`. It is obviously wrong though when you use `min-device-width`. Why to download assets you will never use? But, anyway, we can at least minimize the harm by combining all media queries into single file, thus reducing number of http requests. That is H5BP's approach and it's very sane.

But immediately the problem #2 arises: IElt9 doesn't support media queries at all! Of course we can create a separate CSS file and attach it via conditional comments. To avoid disturbing DRY principle, we can use Sass with its powerful @import command. This method is described in [Nicolas Gallagher's post](http://nicolasgallagher.com/mobile-first-css-sass-and-ie/).

Let's see, what CSS assets we would like to have in our average project:

1. [normalize.css](http://necolas.github.com/normalize.css/) or [reset.css](http://meyerweb.com/eric/tools/css/reset/)
2. print styles (needed for all kind of devices, because today we can print from mobiles, tablets and desktops)
3. common rules for all screen sizes (think background colors, fonts, etc.)

I also developed a small set of Sass @mixins that allows me to avoid writing prefix vendors manually: `_utilities.scss`.

Note the underscore character before the file name. It means that Sass parser won't generate separate file when you run

	scss --watch public/css

The code above will watch `*.scss` files in `/public/css/` for changes and generate or rewrite corresponding CSS files, e.g. `style.scss` will produce `style.css`, but `_utilities.css` will generate nothing. (Generate CSS for production with `-t compressed` option.)

So, let's place our includes into appropriate directory under `/public/css/`:

  includes/_utilities.scss # @mixins with vendor prefixes
  includes/_normalize.scss
  includes/_base.scss # common styles for all devices
  includes/_layout.scss # devices that need complex layouts: tablets, mobiles
  includes/_print.scss

Now we can generate the three files that we need:

1. `style.css` contains CSS for all modern browsers. Yep, one file to rule them all.
2. `ie.css` is prety self-explanatory
3. `ie-mobile.css` is separated from `ie.css`

Please take a look at `templates/base.html` and see how it fits there.

### CoffeeScript (formerly known as JavaScript)

TODO: loading of JS files; RequireJS, Modernizr's matchMedia.js?
