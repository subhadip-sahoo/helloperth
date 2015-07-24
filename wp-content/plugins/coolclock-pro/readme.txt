=== CoolClock - Advanced extension ===
Contributors: RavanH
Tags: clock, analog clock, coolclock, javascript, jquery, widget, shortcode
Requires at least: 2.9
Tested up to: 3.8
Stable tag: 4.0

Extends the CoolClock WordPress plugin.


== Description ==

This plugin extends the [CoolClock](http://wordpress.org/extend/plugins/coolclock/) WordPress plugin with advanced options. 


= Features =

- Show date or 24h digital time
- Background image or color
- Border radius (rounded corners for background))
- Advanced positioning options (relative to background)
- One extra clean skin for use with background image


== Frequently Asked Questions ==

= Where do I start? =

There is no options page. Just go to your Appearance > Widgets admin page and find the new Analog Clock widget. Add it to your sidebar and change settings if you want to see another than the default clock.


= What options does the widget have? =

First of all, you can pick a preset skin. There are 21 skins made by other users and one Minimal skin that only shows the clock arms, that can be useful for placing over a custom background image. Then there are:

- Custom skin parameters - see question below;
- Radius - changes the clock size;
- Hide second hand;
- Show digital time or date;
- GMT Offset - use this if you want one or more clocks to show the time for other timezones;
- Scale - linear is our standard clock scale, the other two show a logarithmic time scale;
- Align - left, center or right;
- Subtext - optional text, centered below the clock.

Then there are extra options availabe in the [CoolClock - Pro extension](http://status301.net/wordpress-plugins/coolclock-pro/) which allow for more customisation:

- Background image - define the full URL or path to an image to serve as background;
- Repeat image;
- Background position - center, top, right, bottom or left of the wrapping div (define div size below);
- Width and height - define the size of the wrapping div that holds the background image;
- Background color - define a color value in hex or rgb(a) format, or a css color name;
- Border radius - optional rounded borders, higher is rounder;
- Clock position relative to background - here you can position the clock relative to top and left border of the wrapping div (as defined above) that holds the background image.


= How can I create a custom skin? =

Here are your first steps into the wonderous world of CoolClock skins ;)

1. Copy the following code to a simple unformatted text document (.txt) on your computer.

`
outerBorder: { lineWidth: 1, radius:95, color: "black", alpha: 1 },
smallIndicator: { lineWidth: 2, startAt: 89, endAt: 93, color: "black", alpha: 1 },
largeIndicator: { lineWidth: 4, startAt: 80, endAt: 93, color: "black", alpha: 1 },
hourHand: { lineWidth: 8, startAt: -15, endAt: 50, color: "black", alpha: 1 },
minuteHand: { lineWidth: 7, startAt: -15, endAt: 75, color: "black", alpha: 1 },
secondHand: { lineWidth: 1, startAt: -20, endAt: 85, color: "red", alpha: 1 },
secondDecoration: { lineWidth: 1, startAt: 70, radius: 4, fillColor: "red", color: "red", alpha: 1 }
`

2. These parameters are the ones from the swissRail skin. Now go and change some parameter values like lineWidth or start/endAt points. The numbers refer to a percentage of the radius, so startAt: 0, endAt: 50 means a line from the center to 50% of the way to the edge. Alpha means the transparency of the element where alpha: 1 means solid. For example alpha: 0.5 means 50% transparent.

3. Go to you the Analog Clock widget, select *Skin: Custom* and copy your modified code (all of it, not just the modified parts!) into the field **Custom skin parameters**. Then save the widget and reload your website front page (or wherever the clock is visible) to see the result of your work.

See the preset skins in [moreskins.js](http://randomibis.com/coolclock/moreskins.js) for more examples. And have fun tweaking!


= Can I share this fantastic custom skin I created? =

If you make a nice skin and would like to share it, then send it to the script creator at simon dot baird at gmail dot com or paste the custom parameters into a new ticket (mark it as 'not a support question') on the Support tab. 

Thanks for sharing! :)

= Can I insert a clock in posts or pages? =

Yes, there is a shortcode available. Start with a default clock by pasting `[coolclock]` into a post.

The following parameters are available:

- **skin** -- must be one of these: 'swissRail' (default skin), 'chunkySwiss', 'chunkySwissOnBlack', 'fancy', 'machine', 'simonbaird_com', 'classic', 'modern', 'simple', 'securephp', 'Tes2', 'Lev', 'Sand', 'Sun', 'Tor', 'Cold', 'Babosa', 'Tumb', 'Stone', 'Disc', 'watermelon' or 'mister'. If the Pro extension is activated, there is also 'minimal' available. Please note that these names are _case sensitive_.
- **radius** -- a number to define the clock radius. Do not add 'px' or any other measure descriptor.
- **noseconds** -- set to true (or 1) to hide the second hand
- **gmtoffset** -- a number to define a timezone relative the Greenwhich Mean Time. Do not set this parameter to default to local time.
- **showdigital** -- set to 'digital12', 'digital24' or 'date' to show the time or date in digital format too
- **scale** -- must be one of these: 'linear' (default scale), 'logClock' or  'logClockRev'. Linear is our normal clock scale, the other two show a logarithmic time scale
- **subtext** -- optional text, centered below the clock
- **align** -- sets floating of the clock: 'left', 'right' or 'center'

Example: `[coolclock skin="chunkySwissOnBlack" radius="140" showdigital=date align="left"]`

Then there are extra options availabe in the [CoolClock - Pro extension](http://status301.net/wordpress-plugins/coolclock-pro/) which allow for more customisation:

- **background_image** - define the full URL or path to an image to serve as background
- **background_height** - give a height in pixels (default: auto = clock plus subtext height)
- **background_width** - give a width in pixels  (default: clock width)
- **background_color** - define a color value in hex or rgb(a) format, or a css color name
- **background_position** - left top, left, left bottom, top, center, bottom, right top, right or right bottom (default: center)
- **background_repeat** - repeat, repeat-x, repeat-y, no-repeat (default: no-repeat)
- **background_border_radius** - optional rounded corners, higher is rounder

Example: `[coolclock skin="minimal" radius="63" align="left" background_image="http://i35.tinypic.com/990wtx.png"]`


== Changelog ==

= 4.0 =
* Digital 24h time
* Date

= 3.0 =
* Shortcode background parameters
* More background position select options
