# Behat AsciiDoc Formatter

[![Latest Stable Version](https://poser.pugx.org/digitalnoise/behat-asciidoc-formatter/v/stable)](https://packagist.org/packages/digitalnoise/behat-asciidoc-formatter)
[![Latest Unstable Version](https://poser.pugx.org/digitalnoise/behat-asciidoc-formatter/v/unstable)](https://packagist.org/packages/digitalnoise/behat-asciidoc-formatter)
[![Build Status](https://travis-ci.org/digitalnoise-de/behat-asciidoc-formatter.svg?branch=master)](https://travis-ci.org/digitalnoise-de/behat-asciidoc-formatter)
[![License](https://poser.pugx.org/digitalnoise/behat-asciidoc-formatter/license)](https://packagist.org/packages/digitalnoise/behat-asciidoc-formatter)

## Installation

```bash
$ composer require --dev digitalnoise/behat-asciidoc-formatter
```

## Configuration

```yaml
default:
    formatters:
        asciidoc:
            output_path: '%paths.base%/reports'
    extensions:
        Digitalnoise\Behat\AsciiDocFormatter:
            title: My Project Title
```
