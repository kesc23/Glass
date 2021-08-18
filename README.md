# Glass
![Release](https://flat.badgen.net/badge/Actual%20Release/v0.6.2/orange?icon=github) ![License](https://flat.badgen.net/badge/license/GPL%20v3/green) ![Next Release](https://flat.badgen.net/badge/Next%20Programmed%20Release/v0.7.0/purple) <h4>You can donate to help this project: 👇👇👇 </h4>
![Bitcoin](https://flat.badgen.net/badge/13erUe3W74iY2jrVAx53bQyjHvZALtWqog/Bitcoin/yellow?icon=bitcoin)
![Dash](https://flat.badgen.net/badge/Xd4VYVdP6s7HSVhSarfzSMjCS2c6esHFtM/Dash/blue?icon=https://cdn.worldvectorlogo.com/logos/dash-5.svg)
![BAT](https://flat.badgen.net/badge/0x7547D7c894b746872c9cf2F57809d7eF2CC04678/BAT/red?icon=https://cdn.worldvectorlogo.com/logos/basic-attention-token.svg)

I created this for helping me to debug and add functionalities in my programs.<br>
It turned out to be one way of helping others to build their PHP programs the way they wanted to, without any hassle.

## Getting Started:

![Packagist](https://flat.badgen.net/badge/packagist/not%20ready/red)
![Release](https://flat.badgen.net/badge/Release/v0.6.2/green?icon=github)<br>
Once you've downloaded Glass from Github, you can put it *preferentially* in your main folder and you can simply call it in your index.php:

```php
# Example of an index.php

require glass/glass.php;
```

In earlier versions you needed to require glass/config.php and then call `glassInit();`<br>
Now, all you need to do is just require the file as shown above.

From now on for better use in glass debbuging functions, if you need to require/include a file inside your script you can use:

```php
glassInclude( 'filename.php', 'path/to/file/' );
# Or
glassRequire( 'filename.php', 'path/to/file/' );
```

They do seem equal, following the same differences shared between require and include functions in php but they have a debugging functionallity. 

<table align="center" >
  <tr>
    <td>
      <img style="margin: auto; border-radius: 4px;" src="https://avatars.githubusercontent.com/u/14094485?v=4" width="180" >
    </td>
  </tr>
</table>
<table align="center" >
  <tr>
    <td>
      <h3 align="center" style="line-height: 0; margin: 0px; padding: 0px;" >Author: Kesc23</h3>
    </td>
  </tr>
  <tr>
    <td>
      <img src="https://flat.badgen.net/badge/Twitter/@kevin_esc23/blue?icon=twitter" >
      <img src="https://flat.badgen.net/badge/Instagram/_kevin.campos/purple?icon=https://www.logo.wine/a/logo/Instagram/Instagram-Glyph-White-Logo.wine.svg" >
      <img src="https://flat.badgen.net/badge/Fiverr/kesc23/green?icon=https://cdn.worldvectorlogo.com/logos/fiverr-1.svg" >
    </td>
  </tr>
</table>
