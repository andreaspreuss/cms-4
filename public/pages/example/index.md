# Examples

## Download file

```
{file_get_contents(\dir\content('example/download/index.phtml'))|noescape}
```

{url '/example/download/'}

## Custom template 

```
{file_get_contents(\dir\content('example/custom-template/index.md'))|noescape}
```

```
{file_get_contents(\dir\content('example/custom-template/template.latte'))|noescape}
```

{url '/example/custom-template/'}

## HTML

```
{file_get_contents(\dir\content('example/html/index.html'))|noescape}
```
{url '/example/html/'}


```
{file_get_contents(\dir\content('example/html/partial.html'))|noescape}
```

{url '/example/html/partial'}