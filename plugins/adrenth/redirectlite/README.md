# Adrenth.RedirectLite

## The 2nd best Redirect plugin for October CMS

This is a **lite version** of the best [Redirect-plugin](http://octobercms.com/plugin/adrenth-redirect) for October CMS. With this plugin installed you can manage redirects directly from October CMS' beautiful interface. Many webmasters and SEO specialists use redirects to optimise their website for search engines. This plugin allows you to manage such redirects with a nice and user-friendly interface.

## What does this plugin offer?

This plugin adds a 'Redirects' section to the main menu of October CMS. This plugin has a unique and fast matching algorithm to match your redirects before your website is being rendered.

## Features

|   | Lite | Pro |
|---|---|---|
| Quick matching algorithm | YES | YES |
| Exact path matching | YES | YES |
| Redirect to external URLs | YES | YES |
| Redirect to internal CMS pages | YES | YES |
| Matching using placeholders (dynamic paths) | NO | YES |
| Match placeholders using regular expressions | NO | YES |
| Importing and exporting redirect rules | NO | YES |
| A test utility for redirects | NO | YES |
| Schedule redirects (e.g. active for 2 months) | NO | YES |
| Redirect log | NO | YES |
| Categorize redirects | NO | YES |
| Statistics | NO | YES |
| Multilingual | NO | YES |
| Caching (boosts performance) | NO | YES |

## Supported database platforms

* MySQL
* PostgreSQL
* SQLite

## Supported HTTP status codes

* HTTP/1.1 301 Moved Permanently
* HTTP/1.1 302 Found
* HTTP/1.1 303 See Other
* HTTP/1.1 404 Not Found
* HTTP/1.1 410 Gone

## Supported HTTP request methods

* `GET`
* `POST`
* `HEAD`

## Performance

All redirects are stored in the database and will be automatically "published" to a file which the internal redirect mechanism uses to determine if a certain request needs to be redirected. This is way faster than querying a database.

This plugin is designed to be fast and should have no negative effect on the performance of your website.

To gain maximum performance with this plugin:

* Use PHP7 (really you should), this increases the performance with 200%
* Maintain your redirects frequently to keep the number of redirects as low as possible.

**The Pro version has a in-memory Caching mechanism built in.**

## Questions? Need help?

If you have any question about how to use this plugin, please don't hesitate to contact me. I'm happy to help you. You can also visit the support forum and drop your questions/issues there.

Kind regards,

Alwin Drenth -- *Author of the Redirect Lite plugin*

---

If you love this quality plugin as much as I do, please **rate my plugin**, [contribute](https://github.com/adrenth/redirectlite-lang) or consider a [PayPal donation](https://www.paypal.me/adrenth) to support this plugin and my other quality October CMS plugins.

---

## Other plugins by [Alwin Drenth](http://octobercms.com/author/Adrenth)

![Redirect](http://octobercms.com/storage/app/uploads/public/590/45e/d44/thumb_7123_64x64_0_0_auto.png)

[**Redirect**](http://octobercms.com/plugin/adrenth-redirect) (Editors' Choice) *Advanced Redirect plugin for October CMS*

![HtmlPurifier](http://octobercms.com/storage/app/uploads/public/588/334/987/thumb_6466_64x64_0_0_auto.png)

[**HtmlPurifier**](http://octobercms.com/plugin/adrenth-htmlpurifier) -  *Adds a standards compliant HTML filter to October CMS.*

![RssFetcher](http://octobercms.com/storage/app/uploads/public/567/69d/038/thumb_3541_64x64_0_0_auto.png)

[**RssFetcher**](http://octobercms.com/plugin/adrenth-rssfetcher) - *Fetches RSS/Atom feeds from different sources to publish on your website or dashboard.*
