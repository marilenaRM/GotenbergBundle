# UPGRADE FROM 1.3.0 to 1.4.0

## Breaking changes
* `AbstractBuilder::getHeadersBag()` now retains `Gotenberg-Webhook-Extra-Http-Headers` as an array until the payload is built. Code reading this value directly must expect an array instead of a JSON string.

## Bug Fixes
* `gotenberg_font_face()` is now declared `is_safe` for both `html` and `css` Twig contexts.
  Previously, using it inside a `<style>` tag would cause Twig's HTML auto-escaping to convert
  double quotes to `&quot;`, producing invalid CSS. If you were using the `| raw` filter as a
  workaround, you can now remove it.

  ```diff
    - <style>{{ gotenberg_font_face('fonts/my-font.woff2', 'MyFont') | raw }}</style>
    + <style>{{ gotenberg_font_face('fonts/my-font.woff2', 'MyFont') }}</style>
  ```
