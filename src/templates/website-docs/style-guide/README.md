<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/Bluegg/craft-template">
    <img src="https://bluegg.co.uk/images/logo.svg" alt="Bluegg's Logo" width="160" style="background: white; padding: 1rem; border-radius: 1rem;">
  </a>

  <h3 align="center">Styleguide</h3>
  <p align="center">Our styleguide for Craft CMS projects. 🔥</p>
  <br />
  <div align="center">
    <a href="https://github.com/Bluegg/craft-template/tree/main">View Craft Template</a>
    <span>|</span>
    <a href="https://github.com/Bluegg/craft-template/tree/main/ansible">View Ansible Guide</a>
  </div>
</div>

<!-- GETTING STARTED -->

## How to see it

All of our styleguides can be viewed in all environments by visiting `<domain>/styleguide`.

## Where to find it

The code behind the styleguides can be found in `/craft/templates/styleguide`.

## How to change it

The styleguide is split into three sections:

### index.twig

This is the landing page for the styleguide. It includes a series of tabs to change between each of the components. New tabs can be added by modifying the `sections` array on `Line 33` of this file.

### \_partials/\*.twig

Each of these files represents a closely-related group of components E.G. Typography, Cards, Media etc.

New partials can be easily added to the styleguide by adding a new name to the `sections` array detailed above, and then creating the .twig file. Please note that the file name should match the entry in the `sections` array and be written in kebab case. E.G.

#### The array in index.twig

```
set sections = ['Calls To Action']
```

#### The file in /\_partials

```
calls-to-action.twig
```

### data.twig

This file contains a collection of data sets to be used by the components. Data should be structured in the same way as the sections and fields setup in Craft to ensure that the components are compatible with both the CMS and the styleguide. E.G.

```js
callToAction: {
    heading: 'This is a heading',
    lede: 'This is a lede',
    button: {
        buttonText: 'This is a button',
        buttonUrl: 'https://bluegg.co.uk',
    },
}
```

The data can be used within the components simply by referencing the objects.

```php
<h1>{{ data.callToAction.heading }}</h1>
```

<!-- COMMENT -->

<br />

<h3 align="center">Happy Coding! 👋🏻</h3>

<!-- BLUEGG LOGO -->

<br />

<p align="center">
  <a href="https://bluegg.co.uk" target="_blank">
    <img src="https://bluegg.co.uk/apple-touch-icon.png" alt="Logo" width="40" height="40" style="border-radius: 0.5rem;">
  </a>
</p>
