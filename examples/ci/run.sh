#!/usr/bin/env bash
# Runs one example against this checkout of the library and compares its output with expected-output.txt:
#   examples/ci/run.sh hello-world
# The example's composer.json points at the repository root (a Composer path repository), so
# `composer install` takes tomaj/nette-auto-variable-template from this checkout, not from Packagist.
set -euo pipefail
examples="$(cd "$(dirname "$0")/.." && pwd)"
root="$(cd "$examples/.." && pwd)"
name="${1:?usage: $0 <example>}"
dir="$examples/$name"
[[ -f "$dir/run.php" && -f "$dir/expected-output.txt" ]] || { echo "fail: $dir has no run.php or expected-output.txt" >&2; exit 1; }
cd "$dir"
rm -rf vendor composer.lock temp actual-output.txt
composer install --no-interaction --no-progress
# fail when Composer took the library from anywhere else
installed="$(cd vendor/tomaj/nette-auto-variable-template && pwd -P)"
[[ "$installed" == "$(cd "$root" && pwd -P)" ]] || { echo "fail: vendor/tomaj/nette-auto-variable-template is $installed, not this checkout" >&2; exit 1; }
php run.php > actual-output.txt
diff -u expected-output.txt actual-output.txt
echo "ok examples/$name matches expected-output.txt"
