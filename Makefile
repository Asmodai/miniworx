# -*- Mode: Makefile -*-
#
# Makefile --- MiniworX makefile.
#
# Copyright (c) 2018 Paul Ward <pward@alertlogic.com>
#
# Author:     Paul Ward <pward@alertlogic.com>
# Maintainer: Paul Ward <pward@alertlogic.com>
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

# Change these to your own versions.  Do not commit this file.
PHP        = php72
PHPCS      = phpcs
PHPMD      = phpmd
PHPMETRICS = phpmetrics
PHPDOC     = phpdoc

SUBDIRS = miniworx public

help:
	@echo 'Valid targets are:'
	@echo '   doc'
	@echo '   metrics'
	@echo '   callgrind'
	@echo '   check'
	@echo '   detector'

doc: $(SUBDIRS)
	@echo 'Running phpdoc'
	@$(PHPDOC) -d $< -t doc/phpdoc

metrics:
	@echo 'Running PHP Metrics'
	@$(PHPMETRICS) --report-html=metrics --level=999 `pwd`

detector: $(SUBDIRS)
	@echo 'Running PHP Multi-Detect'
	@$(PHPMD) `echo '$?' | sed -e 's/ /,/g'`                             \
	          text                                                       \
	          cleancode,codesize,controversial,design,naming,unusedcode  \
	          --suffixes=php

# phpcs allows multiple directories.
check: $(SUBDIRS)
	@echo 'Running PHP Code Sniffer'
	@$(PHPCS) -s                               \
	          -w                               \
	          --standard=`pwd`/etc/ruleset.xml \
	          $?

callgrind:
	@echo 'Generating callgrind data'
	@$(PHP) -d xdebug.profiler_enable=1                  \
	        -d xdebug.profiler_output_dir=`pwd`          \
	        -d xdebug.profiler_output_name=callgrind.out \
	        -f public/index.php

all: help

# Makefile ends here.
