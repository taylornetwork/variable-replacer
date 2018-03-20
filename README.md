# VariableReplacer

This package will replace variables in a string. It includes functionality to use a stage based replace, meaning you can replace multiple variables in the same string at different times.

## Install

Install using Composer

```bash
$ composer require taylornetwork/variable-replacer
```

## Default Options

By default the syntax for a replaced variable is

```php
'@stage{var}'
```

Note no `$` on the variable.


## Usage

Let's say you have a logger class that logs recent activity and you want to define the same message whenever any model is created but want information from the model in the description.

In this example my base model class is defined as:

```php

// App\Models\Model.php

use Illuminate\Database\Eloquent\Model;

abstract class Model
{

    /**
     * All models will define what the model's name is
     *
     * @return string
     */
    abstract public funtion getNameAttribute();

    /**
     * Returns the model name
     *
     * @return string
     */
    public function getModelNameAttribute()
    {
        return class_basename(get_class($this));
    }
}

```

In an observer class you could do the following.

```php

// App\Observers\BaseObserver.php

use TaylorNetwork\VariableReplacer\VariableReplacer;
use App\Models\Model;
use Some\Logger\Package\Logger;

class BaseObserver
{
    protected $descriptions = [
        'created' => 'A @entry{modelName} named @runtime{name} was created.',
    ];
    
    public function created(Model $model) 
    {
        $description = (new VariableReplacer)->stage('entry')
                                             ->replaceWith($model)
                                             ->parse($this->descriptions['created']);
      
        return (new Logger)->log($description);
    }
}

```

*The VariableReplacer gets the entire `App\Models\Model $model` object and as such, all methods are available to be used.* 

If a model `App\Models\Customer` with name `'John Smith'` was created the `$description` that would be saved to the database is

```php
'A Customer name @runtime{name} was created.'
```

When accessing the log you could run the description through the replacer with the `runtime` stage.

```php

// App\Models\Log.php

use TaylorNetwork\VariableReplacer\VariableReplacer;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public function getDescriptionAttribute()
    {
        return (new VariableReplacer)->stage('runtime')
                                     ->replaceWith($this->relatedModel)
                                     ->parse($this->attributes['description']);
    }
}

```

Assuming `'A Customer named @runtime{name} was created.'` was passed the returned value would be

```php
'A Customer named John Smith was created.'
```

### Note

Because the VariableReplacer gets the entire object you pass to the `replaceWith` method, you can chain methods onto it if you need to get relations, etc.

Example:

```php

(new VariableReplacer)->stage('runtime')
                      ->replaceWith($someModel)
                      ->parse('A @runtime{relatedModel->someRelation->someOtherMethod()} did something');

```
