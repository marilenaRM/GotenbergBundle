# UPGRADE FROM 1.1.0 to 1.2.0

## Bug Fixes

* `gotenberg_font_face()` is now declared `is_safe` for both `html` and `css` Twig contexts.
  Previously, using it inside a `<style>` tag would cause Twig's HTML auto-escaping to convert
  double quotes to `&quot;`, producing invalid CSS. If you were using the `| raw` filter as a
  workaround, you can now remove it.

  ```diff
  - <style>{{ gotenberg_font_face('fonts/my-font.woff2', 'MyFont') | raw }}</style>
  + <style>{{ gotenberg_font_face('fonts/my-font.woff2', 'MyFont') }}</style>
  ```
