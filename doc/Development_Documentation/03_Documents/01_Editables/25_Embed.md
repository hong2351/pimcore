# Embed Editable

### Configuration

| Name     | Type   | Description                                                                        |
|----------|--------|------------------------------------------------------------------------------------|
| `width`  | string | Width in the editmode                                                              |
| `height` | string | Height in the editmode                                                             |
| `class`  | string | A CSS class that is added to the surrounding container of this element in editmode |

Additionally you can use any configuration option of https://github.com/mpratt/Embera

## Methods

| Name          | Return    | Description                                                            |
|---------------|-----------|------------------------------------------------------------------------|
| `isEmpty()`   | boolean   | Whether the editable is empty or not                                   |
| `getData()`   | string    | Get the URL of the assigned resource.                                  |

## Example

<div class="code-section">

```php
// Basic usage
<?= $this->embed("socialWidgets"); ?>
 
//Advanced usage
<?= $this->embed("socialWidgets", ["width" => 540]); ?>
```

```twig
{# Basic usage #}
{{ pimcore_embed("socialWidgets") }}

{# Advanced usage #}
{{ pimcore_embed("socialWidgets", { "width": 540 }) }}
```
</div>