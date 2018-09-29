<?php

declare(strict_types=1);

namespace Babelfish\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;

final class Modeline implements Strategy
{
    private const SEARCH_SCOPE = 5;

    private const EMACS_MODELINE = <<<EOT
        /
         -\*-
         (?:
           # Short form: `-*- ruby -*-`
           \s* (?= [^:;\s]+ \s* -\*-)
           |
           # Longer form: `-*- foo:bar; mode: ruby; -*-`
           (?:
             .*?       # Preceding variables: `-*- foo:bar bar:baz;`
             [;\s]     # Which are delimited by spaces or semicolons
             |
             (?<=-\*-) # Not preceded by anything: `-*-mode:ruby-*-`
           )
           mode        # Major mode indicator
           \s*:\s*     # Allow whitespace around colon: `mode : ruby`
         )
         ([^:;\s]+)    # Name of mode
         # Ensure the mode is terminated correctly
         (?=
           # Followed by semicolon or whitespace
           [\s;]
           |
           # Touching the ending sequence: `ruby-*-`
           (?<![-*])   # Don't allow stuff like `ruby--*-` to match; it'll invalidate the mode
           -\*-        # Emacs has no problems reading `ruby --*-`, however.
         )
         .*?           # Anything between a cleanly-terminated mode and the ending -*-
         -\*-
        /xi
EOT;
    private const VIM_MODELINE = <<<EOT
        /
         # Start modeline. Could be `vim:`, `vi:` or `ex:`
         (?:
           (?:[ \t]|^)
           vi
           (?:m[<=>]?\d+|m)? # Version-specific modeline
           |
           [\t\x20] # `ex:` requires whitespace, because "ex:" might be short for "example:"
           ex
         )
         # If the option-list begins with `set ` or `se `, it indicates an alternative
         # modeline syntax partly-compatible with older versions of Vi. Here, the colon
         # serves as a terminator for an option sequence, delimited by whitespace.
         (?=
           # So we have to ensure the modeline ends with a colon
           : (?=[ \t]* set? [ \t] [^\n:]+ :) |
           # Otherwise, it isn't valid syntax and should be ignored
           : (?![ \t]* set? [ \t])
         )
         # Possible (unrelated) `option=value` pairs to skip past
         (?:
           # Option separator. Vim uses whitespace or colons to separate options (except if
           # the alternate "vim: set " form is used, where only whitespace is used)
           (?:
             [ \t]
             |
             [ \t]* : [ \t]* # Note that whitespace around colons is accepted too:
           )                 # vim: noai :  ft=ruby:noexpandtab
           # Option's name. All recognised Vim options have an alphanumeric form.
           \w*
           # Possible value. Not every option takes an argument.
           (?:
             # Whitespace between name and value is allowed: `vim: ft   =ruby`
             [ \t]*=
             # Option's value. Might be blank; `vim: ft= ` says "use no filetype".
             (?:
               [^\\ \t] # Beware of escaped characters: titlestring=\ ft=ruby
               |          # will be read by Vim as { titlestring: " ft=ruby" }.
               \\.
             )*
           )?
         )*
         # The actual filetype declaration
         [ \t:] (?:filetype|ft|syntax) [ \t]*=
         # Language's name
         (\w+)
         # Ensure it's followed by a legal separator
         (?=[ \t]|:|\n)
        /xi
EOT;


    /**
     * @return Language[]
     */
    public function getLanguages(SourceFile $file): array
    {
        $content = $this->getHeaderAndFooter($file);

        preg_match_all(self::EMACS_MODELINE, $content, $matches_emacs);
        preg_match_all(self::VIM_MODELINE, $content, $matches_vim);

        $aliases = array_merge($matches_emacs[1], $matches_vim[1]);

        if (empty($aliases)) {
            return [];
        }

        $language = Language::findByAlias($aliases[0]);

        if ($language === null) {
            return [];
        }

        return [$language];
    }

    private function getHeaderAndFooter(SourceFile $file): string
    {
        $lines = $file->getLines();

        if (\count($lines) <= self::SEARCH_SCOPE * 2) {
            return \implode("\n", $lines) . "\n";
        }

        return \implode("\n", \array_slice($lines, 0, self::SEARCH_SCOPE)) . "\n" .
            \implode("\n", \array_slice($lines, -self::SEARCH_SCOPE, self::SEARCH_SCOPE)) . "\n";
    }
}