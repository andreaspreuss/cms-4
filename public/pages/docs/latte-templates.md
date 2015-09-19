<!--
id: latte-templates
title: How to use Latte Templates
template: ../../layout.docs.latte
-->

<style>table th:first-child { width: 50%; }</style>

# Default Latte Macros 

### Expressions and variables

| Latte Syntax      | Description
| ----------------- | ----------------
| `{l}$variable{r}` | prints an escaped variable content
| `{l}$variable|noescape{r}` | prints a variable without escaping  
| `{l}expression{r}` | prints an escaped expression  
| `{l}expression|noescape{r}` | prints an expression without escaping
  
### Conditions

| Latte Syntax      | Description
| ----------------- | ----------------
| `{l}if $cond{r} … {l}elseif $cond{r} … {l}else{r} … {l}/if{r}` | if condition
| `{l}ifset $cond{r} … {l}elseifset $cond{r} … {l}/ifset{r}` | if (isset()) condition
 
### Variables

| Latte Syntax      | Description
| ----------------- | ----------------
| `{l}var $foo = value{r}` | create variable with value 
| `{l}default $foo = value{r}` | default value when variable isn't declared
| `{l}capture $var{r} … {l}/capture{r}` | captures a section to a variable

### Engine

| Latte Syntax      | Description
| ----------------- | ----------------
| `{l}capture $var{r} … {l}/caopture{r}` | captures a section to a variable
| `{l}l{r} or {l}r{r}` | print {l} and {r} characters, respectively 

### HTML tag attributes

| Latte Syntax      | Description
| ----------------- | ----------------
| `n:class` | smart class attribute
| `n:attr` | smart HTML attributes
| `n:ifcontent` | Omit empty HTML tag
| `n:tag-if` | Omit HTML tag if condition is FALSE

### Blocks, layouts, template inheritance

| Latte Syntax      | Description
| ----------------- | ----------------
| `{l}block block{r}` | block definition and immediate print out 
| `{l}define block{r}` | block defintion for future use 
| `{l}include block{r}` | inserts a block 
| `{l}include mytemplate.latte{r}` | insert a template 
| `{l}includeblock 'file.latte'{r}` | loads blocks from external template 
| `{l}extends 'file.latte'{r}` | alias for {l}layout{r} 
| `{l}ifset #block{r} … {l}/ifset{r}` | condition if block is defined 





Here is [summary and description of all default Latte template engine macros](https://doc.nette.org/en/2.3/default-macros).