#### v.1.5.2 (2016-10-29)
* allow empty octet strings

#### v.1.5.1 (2015-10-02)
* add keywords to composer.json (this is a version on its own so the keywords are found on a stable version at packagist.org)

#### v.1.5.0 (2015-10-30)
* fix a bug that would prevent you from decoding context specific tags on multiple objects [#57](https://github.com/fgrosse/PHPASN1/issues/57)
  - `ExplicitlyTaggedObject::__construct` does now accept multiple objects to be tagged with a single tag
  - `ExplicitlyTaggedObject::getContent` will now always return an array (even if only one object is tagged)

#### v.1.4.2 (2015-09-29)
* fix a bug that would prevent you from decoding empty tagged objects [#57](https://github.com/fgrosse/PHPASN1/issues/57)

#### v.1.4.1
* improve exception messages and general error handling [#55](https://github.com/fgrosse/PHPASN1/pull/55)

#### v.1.4.0
* **require PHP 5.6**
* support big integers (closes #1 and #37)
* enforce one code style via [styleci.io][9]
* track code coverage via [coveralls.io][10]
* replace obsolete `FG\ASN1\Exception\GeneralException` with `\Exception`
* `Construct` (`Sequence`, `Set`) does now implement `ArrayAccess`, `Countable` and `Iterator` so its easier to use
* add [`TemplateParser`][11]
