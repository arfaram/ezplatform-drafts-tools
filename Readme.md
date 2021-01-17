[![GitHub tag (latest SemVer)](https://img.shields.io/github/v/tag/arfaram/draftstoolsbundle?style=flat-square&color=blue)](https://github.com/arfaram/draftstoolsbundle/tags)
[![Downloads](https://img.shields.io/packagist/dt/arfaram/draftstoolsbundle?style=flat-square&color=blue)](https://packagist.org/packages/arfaram/draftstoolsbundle)
[![License](https://img.shields.io/packagist/l/arfaram/draftstoolsbundle.svg?style=flat-square&color=blue)](https://github.com/arfaram/draftstoolsbundle/blob/master/LICENSE)

# Drafts Tools Bundle

This bundle allows you to have access to all user drafts by adding a new API layer. eZPlatform is designed to get only access to drafts for current logged in user over the public API. The Rest API operating on top is requiring additional user token to have access though. 

## Requirement

- eZPlatform by Ibexa 3.x +
- PHP 7.3+

## Features

- Access to private drafts in the everyone dashboard block
- All draft tab contains information about future draft location(s)
- Custom policy to access the new tab
 
## Installation

```
composer require arfaram/draftstoolsbundle
```

- Activate the Bundle in bundles.php

```
    return
    [
        //...
        EzPlatform\DraftsToolsBundle\EzPlatformDraftsToolsBundle::class => ['all' => true],

```

## Usage

User must have below both policies to access the `All drafts` Tab:
- Content / Versionread
- Drafts Tools / All Dashboard Tab

Default pagination value ist set to 25. You can amend this value from the services.yaml e.g:

```
parameters:
    pagination.dashboard_all_drafts_limit: XX
```

## Future Features

- Publish drafts from the Dashboard
- Pagination value in User settings(individual) or add new policies attribute(Global per User group)
- Preview draft link for external user
- Rest API endpoint

**Contributions are welcome**

## Screenshots

### eZ Platform 3.2 


![eZPlatform by Ibexa Drafts Tools Bundle](doc/all_drafts_tab_3.2.png?raw=true "eZPlatform by Ibexa Drafts Tools Bundle")

![eZPlatform by Ibexa Drafts Tools Bundle](doc/draft_locations_3.2.png?raw=true "eZPlatform by Ibexa Drafts Tools Bundle")

### eZ Platform v3.0 and 3.1

![eZPlatform by Ibexa Drafts Tools Bundle](doc/all_drafts_tab.png?raw=true "eZPlatform by Ibexa Drafts Tools Bundle")

![eZPlatform by Ibexa Drafts Tools Bundle](doc/draft_locations.png?raw=true "eZPlatform by Ibexa Drafts Tools Bundle")


