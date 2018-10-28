Stylex
======

Styleguide toolkit for X (everything)

## Use-cases:

* Language styleguides (i.e. php, javascript, markdown, etc)
* Organization specific coding conventions
* File formatting styleguides (i.e. whitepace usage in YAML files, etc)
* Technical writing style guide
* Tone-of-Voice style guide
* Branding style guide (usage of logos, brand colors, fonts, etc)

## Usage

Globally install stylex through composer:

    composer global require linkorb/stylex

Now `cd` into a `-style-guide` directory and run:

    stylex template path-to-my-templates build/

The `template` command will load the style guide files (yaml, json, md, etc) from the current directory, and run them through the (handlebars) templates in your template directory.
The generated output files will be saved in `build/`

## Example

Check out the `example/` directory.

To build this style-guide, simply run:

    cd example/
    ../bin/stylex template ../templates/basic/ build/
    open build/index.md

## Awesome style guides:

* [StyleGuide.io](http://styleguides.io/)
* [Mailchimp's Content Style Guide](https://styleguide.mailchimp.com/)
* [Atlassian Design](https://atlassian.design/)
* [Atlassian Product Style Guide](https://atlassian.design/guidelines/product/overview)
* [Google Developer Documentation Style Guide](https://developers.google.com/style/)

Want to suggest other awesome style guides for this sections? Just send a PR!

## License

MIT (see [LICENSE.md](LICENSE.md))

## Brought to you by the LinkORB Engineering team

<img src="http://www.linkorb.com/d/meta/tier1/images/linkorbengineering-logo.png" width="200px" /><br />
Check out our other projects at [linkorb.com/engineering](http://www.linkorb.com/engineering).

Btw, we're hiring!