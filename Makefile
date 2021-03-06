# -*- Mode: Makefile -*-
#
# Makefile --- MiniworX makefile.
#
# Copyright (c) 2018 Paul Ward <asmodai@gmail.com>
#
# Author:     Paul Ward <asmodai@gmail.com>
# Maintainer: Paul Ward <asmodai@gmail.com>
# Created:    04 Aug 2018 22:12:06
#
#{{{ License:
#
# Permission is hereby granted, free of charge, to any person
# obtaining a copy of this software and associated documentation
# files (the "Software"), to deal in the Software without
# restriction, including without limitation the rights to use, copy,
# modify, merge, publish, distribute, sublicense, and/or sell copies
# of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be
# included in all copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
# EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
# MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
# NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
# BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
# ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
# CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
# SOFTWARE.
#
#}}}
#{{{ Commentary:
#
#}}}

OS         := $(shell uname -s)
ifeq ($(OS), Darwin)
# Because Apple are special.
  PHP      ?= /opt/local/bin/php
else
  PHP      ?= php
endif
COMPOSER   ?= composer
PHPCS       = `pwd`/vendor/bin/phpcs
PHPMD       = `pwd`/vendor/bin/phpmd
PHPMETRICS  = `pwd`/vendor/bin/phpmetrics
PHPDOC      = `pwd`/vendor/bin/phpdoc
APIDOC      = `pwd`/node_modules/apidoc/bin/apidoc

ROUTESDIR = routes
SUBDIRS   = application routes public

MINIWORX_METRICS = doc/metrics/miniworx.html
ROUTES_METRICS   = doc/metrics/routes.html
PUBLIC_METRICS   = doc/metrics/public.html

HTTP_PORT = 8080

.PHONY: doc metrics detector check detector profile

all: help

help:
	@echo 'Valid targets are:'
	@echo '   autoload    -- Regenerate autoloader.   [requires composer]'
	@echo '   deps        -- Make dependencies.       [requires composer]'
	@echo '   doc         -- Make documentation.      [requires phpdoc]'
	@echo '   apidoc      -- Make API documentation.  [requires apidoc]'
	@echo '   metrics     -- Make metrics report.     [requires phpmetrics]'
	@echo '   profile     -- Profile the code.        [requires XDebug]'
	@echo '   check       -- Check code sanity.       [requires phpcs]'
	@echo '   mess        -- Check code quality.      [requires phpmd]'
	@echo '   release     -- build a release          [requires composer]'
	@echo '   help        -- Show this message.       [requires eyesight]'
	@echo '   tools       -- Show tool locations.'
	@echo '   php-version -- See which version of PHP is used by Make.'
	@echo '   run         -- Run via inbuilt PHP web server.'
	@echo '   clean       -- Remove generated files.'

tools:
	@echo 'Tool locations:'
	@echo "   OS:         $(OS)"
	@echo "   PHP:        $(PHP)"
	@echo "   composer:   $(COMPOSER)"
	@echo "   phpcs:      $(PHPCS)"
	@echo "   phpmd:      $(PHPMD)"
	@echo "   phpmetrics: $(PHPMETRICS)"
	@echo "   phpdoc:     $(PHPDOC)"
	@echo "   apidoc:     $(APIDOC)"

clean:
	@echo 'Cleaning junk.'
	-rm -rf doc/metrics
	-rm -rf doc/phpdoc
	-rm -rf doc/apidoc

php-version:
	@$(PHP) --version

autoload:
	@echo 'Updating autoloader.'
	@composer dump-autoload

deps:
	@echo 'Updating/installing dependencies.'
	@npm install apidoc
	@composer update
	@composer install

release:
	@echo 'Building a release.'
	@composer install --no-dev
	@composer dump-autoload

doc: $(SUBDIRS)
	@echo 'Running phpdoc.'
	@$(PHPDOC) -d `echo '$?' | sed -e 's/ /,/g'` -t doc/phpdoc

apidoc:
	@echo 'Running apidoc'
	@$(APIDOC) -i $(ROUTESDIR) -o doc/apidoc

metrics:
	@echo 'Running PHP Metrics.'
	@$(PHPMETRICS) --report-html=$(MINIWORX_METRICS) -- application
	@$(PHPMETRICS) --report-html=$(ROUTES_METRICS)   -- routes
	@$(PHPMETRICS) --report-html=$(PUBLIC_METRICS)   -- public

mess: $(SUBDIRS)
	@echo 'Running PHP Multi-Detect.'
	@$(PHPMD) `echo '$?' | sed -e 's/ /,/g'` \
	          text                           \
	          `pwd`/phpmd.xml                \
	          --suffixes=php

# phpcs allows multiple directories.
check: $(SUBDIRS)
	@(test -d 'vendor/squizlabs/php_codesnifer/src/Standards/Security' \
	  || sh vendor/pheromone/phpcs-security-audit/symlink.sh)
	@echo 'Running PHP Code Sniffer.'
	@$(PHPCS) -s                         \
	          -w                         \
	          --standard=`pwd`/phpcs.xml \
	          $?

profile: 
	@echo 'Generating callgrind data.'
	@HTTP_POST='test1=testing&test2=42'                     \
	 REQUEST_METHOD='GET'                                   \
	 REQUEST_URI='/shrines/eastworld/view?arg1=two&arg2=42' \
	 $(PHP) -d xdebug.profiler_enable=1                     \
	        -d xdebug.profiler_output_dir=`pwd`             \
	        -d xdebug.profiler_output_name=callgrind.out    \
	        -f public/index.php

run:
	@echo 'Running PHP web server.'
	@$(PHP) -S localhost:$(HTTP_PORT) -t public

# Makefile ends here.
