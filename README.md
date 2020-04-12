# PerWorldViewDistance
Setting view distance to a low value may/may not significantly contribute to reducing server load by decreasing the number of chunk requests.
This performance boost is usually very significant in non-static worlds (i.e worlds that have a lot of terrain changes, block ticking etc going on).<br>
HOWEVER, if your world is pretty static, f.e a world dedicated to PvP arenas, you may as well up the view distance so players can get a good view of the mountains in the background.

In some cases, a few servers have both worlds. For example: `world` and `nether` are modifyable by user BUT `battlefield` is read-only which is where this plugin helps.
Heck you may just use it for whichever case you like idc, who am I to set restrictions.

## Usage
Drag and drop the `.phar` file in your `plugins/` folder, set up the `plugin_data/PerWorldViewDistance/config.yml` to your likings and start the server.<br>
If you're stupid and didn't read the comments in `config.yml`, the server will crash. This is because the maximum view distance that you set in your `config.yml:view-distances` should be the value you set to `server.properties:view-distance`.<br>
For example, this is the part of my `config.yml`:
```yaml
view-distances:
  world: 8
  nether: 8
  battlefield: 16
  cooler_battlefield: 32
```
Clearly, as you can make out, the max view distance listed in `view-distances` is `32`. This is the value you must set in `server.properties` to `view-distance`.
This plugin cannot magically hack the server and set the view distance to whatever it likes. It strictly complies with `server.properties`.

## Behaviour
| Max view distance of world   |      Player requested view distance      |  Resulting view distance |
|----------|:-------------:|------:|
| 8 | 16  | 8  |
| 16 | 16 | 16 |
| 32 | 16 | 16 |
