# Tagging Tools Synonyms

The [Tagging Tools](https://github.com/svivian/q2a-tagging-tools) plugin allows you to create "tag synonyms" which map similar tags to each other.
For example, the synonym `pt,paratext` would automatically convert the tag `pt` to `paratext` when a post is submitted.
It also has the ability to retroactively apply these synonyms.
Lastly, it can remove tags altogether by placing them on standalone lines.
Since we are automatically generating tag data for posts, it will be useful to apply these synonyms retroactively.

Below is a list of tag synonyms to apply. Copy and paste all desired rules into the Tagging Tools' admin form.

**Abbreviations**
```
3.0,usfm-3.0
7.5,paratext-7.5
7.6,paratext-7.6
8.0,paratext-8
9.1,paratext-9.1
9.2,paratext-9.2
10,windows-10
as,arabic-script
caps,capitalization
ch,chorus-hub
lite,paratext-lite
nt,new-testament
ot,old-testament
p8,paratext-8
p9,paratext-9
pa6,publishing-assistant-6
paratext8,paratext-8
pa,publishing-assistant
pt7.5,paratext-7.5
pt8,paratext-8
pt9,paratext-9
pt,paratext
rs,roman-script
```

**Plurality**
```
accounts,account
additions,addition
books,book
boxes,box
callers,caller
categories,category
changes,change
chapters,chapter
characters,character
checks,check
codes,code
colors,color
computers,computer
comments,comment
conflicts,conflict
converters,converter
dates,date
diacritics,diacritic
dictionaries,dictionary
entries,entry
errors,error
expressions,expression
features,feature
files,file
folders,folder
fonts,font
footnotes,footnote
forms,form
freezes,freeze
gloss,glossary
headings,heading
hyphens,hypen
ids,id
images,image
improvements,improvement
installers,installer
introductions,introduction
issues,issue
items,item
keyboards,keyboard
languages,language
letters,letter
links,link
lists,list
machines,machine
matches,match
markers,marker
members,member
messages,message
modules,module
names,name
notes,note
organizations,organization
parallels,parallel
parses,parse
passages,passage
plans,plan
practices,practice
problems,problem
programs,program
proposals,proposal
questions,question
quotes,quote
references,reference
renderings,rendering
reports,report
requirements,requirement
resources,resource
results,result
roles,role
rules,rule
setting,settings
screens,screen
scripts,script
scriptures,scripture
shows,show
spaces,space
stages,stage
stems,stem
suggestions,suggestion
symbols,symbol
tablets,tablet
tags,tag
tasks,task
terms,term
texts,text
thousands,thousand
translations,translation
translators,translator
updates,update
users,user
verses,verse
versions,version
videos,video
words,word
```

**Misspellings and Refactors**
```
administrator,admin
anti-virus,antivirus
autocorrect.txt,autocorrect
find/replace,find-replace
heb/grk,hebrew-greek
hyphenatedwords.txt,hyphen
interlinearization,interlinear
registery.paratext.org,registry
send/receive,send-receive
settings.xml,settings
spell,spellcheck
s/r,send-receive
```

**Tags to remove**
```
1:2
way
```