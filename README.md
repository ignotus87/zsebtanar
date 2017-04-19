﻿[![Join the chat at https://gitter.im/zsebtanar-dev/Lobby](https://badges.gitter.im/zsebtanar-dev/Lobby.svg)](https://gitter.im/zsebtanar-dev/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

# Introduction

Zsebtanár is a collection of interactive Maths exercises.

# Setup

## For Windwos

You need to have a running *PHP Server* to run the website and a working internet connection for best display.

1. Download the repository.
2. Unzip the file and copy the `zsebtanar` folder in your `public_html` folder (or `htdocs`, you are using *Xampp*).
3. Make sure path to project folder is correct in `application/config/config.php` in line 28.
4. Create user and database (e.g. through *phpMyAdmin*).
5. Copy username, password and database name in `application/config/database.php` in lines 80-82.
6. Copy the following URL in your browser: `http://localhost/zsebtanar/action/setup`.
7. If everything is set up correctly, the website can be reached through the URL: `http://localhost/zsebtanar/`.

*Note:* If special characters don't apper properly, check the database character set. In order to use Hungarian special characters, use `latin2_hungarian_ci`.


## For Docker   

**Only** for development.

It tested on Ubuntu and Windows 10 with Docker 1.13

1. [Install Docker](https://docs.docker.com/engine/installation/)
2. Clone project `git clone https://github.com/zsebtanar/zsebtanar.git`
2. step into the docker folder `cd /path/to/zsebtanar/docker`
3. Run the docker container: `docker-compose up`        
5. Use this url [http://localhost/action/setup](http://localhost/action/setup) for the DB initialization
6. If everything is set up correctly, the website can be reached through the URL: [http://localhost/](http://localhost/).

Notes:  
- The `docker/Dockerfile` contains environment variables and the `mysql-setup.sh` create the database
- You must run all `docker-compose` commands in the docker folder
- Open interactive bash terminal: `docker-compose exec zsebtanar /bin/bash`
- Run docker in background `docker-compose up -d`    
- Stop docker `Ctrl+C` or if you run in background `docker-compose stop`
- More options `docker-compoes --help`

# Working with client side code

0. Install [nodejs](https://nodejs.org/en/)
1. Install dependencies `npm install`
2. Run one of the build command:
   - `npm run dev` when you are working on the code
   - `npm run build` before you push your changes into the repository

# Add new exercise
In order to add new exercise, you have to do the following steps:

1. Include exercise data in the JSON-file (or create new file if neede).
2. Create a PHP class to generate exercise.
3. Update database by copying the following URL in your browser: `http://localhost/zsebtanar/action/setup`.

## STEP 1: Add exercise info to JSON-file

Exercises are stored in JSON-files *public/resources* folder. For better overview, each class has a separate JSON-file containing the corresponding exercises.

**Note**: The classes on the main page will appear in the same order as the files appears in the *public/resources* folder.

The hierachy for each JSON-file is the following:

1. Class
2. Topic
3. Subtopic
4. Exercise

Each of them *must* have a `name` attribute. Classes, subtopics and exercises *must* have an additional `label` attribute, which is the name of the PHP class file (avoid space and accents). Exercises *can* have additional attributes:

- `ex_order`: order of exercise (if not set, order of exercise will be generated automatically)
- `level`: how many times user has to solve exercise to complete it (default value: **3**)
- `difficulty`: how much time needed to solve problem (**1**: 1-2 mins, **2**: 2-5 mins, **3**:5+ mins)
- `tags`: tags separated by comma (`,`) or semicolon (`;`)
- `link`: you can create a link to an existing exercise by providing path to it in the following form: "Classlabel/Subtopiclabel/Exerciselabel". **Note**: If you create a link to an existing exercise, you don't need to do provide tags and you can skip **STEP 2** as well.
- `dependencies`: these are those "warm-up" exercises which are recommended to solve beforehand. For each such exercise, the following data need to be provided:
    - `classLabel`: class label of exercise
    - `subtopicLabel`: subtopic label of exercise
    - `exerciseLabel`: exercise label

This is a sample for `data.json`:
```
{
    "classes": [
        {
            "label": "5",
            "name": "5. osztály",
            "topics": [
                {
                    "name": "Alapok",
                    "subtopics": [
                        {
                            "name": "Számolás",
                            "label": "Counting",
                            "exercises": [
                                {
                                    "label": "count_apples",
                                    "name": "Számolás 1-től 20-ig",
                                    "tags": "alapműveletek;számolás"
                                },
                                {
                                    "label": "parity",
                                    "name": "Páros vagy páratlan?",
                                    "level": "4",
                                    "tags": "páros,páratlan"
                                },
                                {
                                    "label": "parity2",
                                    "name": "Páros vagy páratlan?",
                                    "link": "5/Counting/count_apples",
                                    "dependencies": [
                                        {
                                            "classLabel": 5,
                                            "subtopicLabel": "Counting",
                                            "exerciseLabel": "count_apples"
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                }
            ]
        }
    ]
}
```


## STEP 2: Create PHP class to generate exercise

1. Create a file `ExerciseLabel.php` in the `/application/libraries/ClassLabel/SubtopicLabel` folder where `ClassLabel`, `SubtopicLabel` and `ExerciseLabel` is equal to `label` of the class, subtopic and the exercise as appears in the JSON-file.
2. Define function called `Generate($level)`.
    - The input is *always* one parameter (`$level`), which is the level of exercise - this can be used to set the difficulty of the exercise.
    - Each exercise *must* be provided with the following return values:
        1. `'question'`: the main body of the exercise,
        2. `'solution'`: this is what the user will see if the answer is wrong.
        2. `'correct'`: correct answer that will be used to compare the user's answer against.
    - Each exercise *can* be provided with additional return values:
        1. `'type'`: exercise type,
        2. `'hints'`: explanation for exercise,
        3. `'labels'`: labels for input fields,
        4. `'youtube'`: youtube video ID.

You can use **MathJax** to display formulas.

### Exercise types

You can create different types of exercise:

#### 1. Integer (default)

User has to send an integer as an answer. To use this you have to return an integer in the `$correct` variable.

Example:
```
/* Guess number */
class Guess_number {
    function Generate($level) {

        $num = rand($level, 3*$level);

        $question = 'How many is $2\cdot'.$num.'$?';
        $correct = 2*$num;
        $solution = '$'.$correct.'$'; // use MathJax for better display

        return array(
            'question'      => $question,
            'correct'       => $correct,
            'solution'      => $solution
        );
    }
}
```

#### 2. Quiz
User has to choose one answer for given options. To use this you have to return the following values:

1. `$options`: array containing options
2. `$correct`: key of correct option

Example:
```
/* Define parity of number */
class Parity {
    function Generate($level) {

        $num = rand($level, 3*$level);

        $question = 'Is the following even or odd?$$'.$num.'$$';

        $options = array('even', 'odd');
        $index = $num%2;
        $solution = $options[$index];

        shuffle($options); // shuffle options
        $correct = array_search($solution, $options); // search key of correct answer

        return array(
            'question'  => $question,
            'options'   => $options,
            'correct'   => $correct,
            'solution'  => $solution
        );
    }
}
```

#### 3. Multi
User has to choose one or more answer for given options. To use this you have to return the following values:

1. `$options`: array containing options
2. `$correct`: array of **0**s and **1**s (for wrong and correct options, respectively)

Example:
```
/* Classify square */
class Square {
    function Generate($level) {

        $question = 'What is a square?';
        $options = array('rectangle', 'parallelogram', 'circle');
        $correct = array(1, 1, 0);
        $solution = 'The square is a rectangle and a parallelogram but not a circle.';

        return array(
            'question'  => $question,
            'options'   => $options,
            'correct'   => $correct,
            'solution'  => $solution,
            'type'      => 'multi'
        );
    }
}
```
#### 4. Fraction
User has to return a fraction. To use this you have to return the following values:

1. `$correct`: array containing numerator and denominator

Example:
```
/* Define reciprocal of fraction */
class Reciprocal {
    function Generate($level) {

        $num = rand(1, $level);
        $denom = rand(1, $level);

        $question = 'What is the reciprocal of the following fraction?$$\frac{'.$num.'}{'.$denom.'}$$';
        $correct = array($denom, $num);
        $solution = '$\frac{'.$denom.'}{'.$num.'}$';

        return array(
            'question'  => $question,
            'correct'   => $correct,
            'solution'  => $solution,
            'type'      => 'fraction'
        );
    }
}
```
#### 5. Array
User has to return an array of numbers. Order is important.

1. `$correct`: array containing correct numbers.
2. `$labels` (optional): labels for each input field

Example:
```
/* Define quotient and remainer */
class Division {
    function Generate($level) {

        $dividend = rand(1, $level);
        $divisor = rand(1, $level);

        $quotient = ceil($dividend/$divisor);
        $remain = $dividend % $divisor;

        $question  = 'What is the result of the following division?$$'.$dividend.':'.$divisor.'=?$$';
        $correct   = array($quotient, $remain);
        $labels    = array('quotient', 'remain');
        $solution  = 'The quotient is $'.$quotient.'$ and the remain is $'.$remain.'$';


        return array(
            'question'  => $question,
            'correct'   => $correct,
            'labels'    => $labels,
            'solution'  => $solution,
            'type'      => 'array'
        );
    }
}
```
#### 6. List
User has to return an array of numbers. Order is not important.

1. `$correct`: array containing correct numbers.
2. `$labels` (optional): labels for each input field

Example:
```
/* Define quotient and remainer */
class Sqrt {
    function Generate($level) {

        $num = pow(rand(1, $level), 2);
        $sqrt = sqrt($num);

        $question  = 'What is square root of $'.$num.'$?';
        $correct   = array(-sqrt($num), sqrt($num));
        $labels    = array('$x_1$', '$x_2$');
        $solution  = '$x_1='.$sqrt.'$, and $x_2=-'.$sqrt.'$';


        return array(
            'question'  => $question,
            'correct'   => $correct,
            'labels'    => $labels,
            'solution'  => $solution,
            'type'      => 'list'
        );
    }
}
```
#### 6. Custom types
In order to add a custom type:

1. Choose name for type (e.g. *custom*)
2. Create new display in `application->Views->Input->Custom.php`
3. Add custom display in `application->Views->Body->Exercise`
4. Define way to compare user's answer to correct solution in `application->Models->Check->GenerateMessages`


### Hints
You can generate hints for exercises. In this case you have to return an extra variable in the end of the function, e.g.:
```
return array(
    'question'  => $question,
    'correct'   => $correct,
    'solution'  => $solution,
    'hints'     => $hints
);
```
The structure of the hints can be the following:

#### A) Single-page
In single-page mode hints will be displayed under each other. In this case the variable `$hints` must be an array containing the hints. E.g.:
```
$hints[] = 'This is hint one.';
$hints[] = 'This is hint two.';
$hints[] = 'This is hint three.';
```
#### B) Multi-page
In multi-page mode hints of the next page will replace earlier hints. In this case the variable `$hints` must be an array containing subarrays, where each subarray contains the hints for the given page. E.g.:
```
$page[] = 'This is hint 1 on page 1.';
$page[] = 'This is hint 2 on page 1.';
$page[] = 'This is hint 3 on page 1.';
$hints[] = $page;

$page = []; // empty array
$page[] = 'This is hint 1 on page 2.';
$page[] = 'This is hint 2 on page 2.';
$page[] = 'This is hint 3 on page 2.';
$hints[] = $page;
```
#### C) Details
If you want to provide details for a specific hint, you need to add an array after the hint. E.g.:
```
$hints[] = 'This is a hint.';
$hints[] = array('This is', 'some details', 'about the hint.');
```
Or if you are using multi-page hints:
```
$page[] = 'This is hint 1 on page 1.';
$page[] = 'This is hint 2 on page 1.';
$page[] = array('This is', 'some details', 'about the hint.');
$hints[] = $page;

$page = []; // empty array
$page[] = 'This is hint 1 on page 2.';
$page[] = 'This is hint 2 on page 2.';
$page[] = array('This is', 'some details', 'about the hint.');
$hints[] = $page;
```
The program will concatenate the detail elements and add a button after the hint. If the user clicks on the hint, he will see the details.

**Note**: Details can only be added to the last hint of a page.

### Additional features
#### 1. Using pictures
In order to include picture into exercise:

1. Upload pictures in `public->resources->exercises` folder
2. Include picture using *base_url()* function. E.g.:

```
$question = 'How many apples are there in the tree?
    <div class="text-center">
        <img class="img-question" height="200px" src="'.base_url().'resources/exercises/count_apples/tree1.png">
    </div>';
```

To generate pictures, you can use the **SVG** functions (see more: http://www.w3schools.com/svg/default.asp)

#### 2. Built in functions
You can find additional functions `application/helpers` folder:

1. `language_helper.php`: language functions (e.g. for suffixes and prefixes),
2. `maths_helper.php`: mathematical functions,
3. `draw_helper.php`: graphical functions (for creating svg).

**Note**: The following list of functions may not be exhaustive! Please check the files for new functions.

##### Language functions #####

|**Name**|**Description**|
|---|---|
|```Times```|Add suffix 'times' to number (szor/szer/ször)|
|```Times2```|Add modified suffix 'times' to number (szorosára/szeresére/szörösére)|
|```Fraction```|Add modified suffix 'th' to number (od/ed/öd)|
|```The```|Add article to number|
|```Dativ```|Add suffix dativus to number (at/et/öt/t)|
|```By```|Add suffix 'by' to number (nál/nél)|
|```With```|Add suffix 'with' to number (val/vel)|
|```To```|Add suffix 'to' to number (hoz/hez/höz)|
|```In```|Add suffix 'in' to number (ban/ben)|
|```On```|Add suffix 'on' to number (ra/re)|
|```On2```|Add suffix 'on' to number (on/en/ön)|
|```From```|Add suffix 'from' to number (ból/ből)|
|```NumText```|Write down number with letters|
|```NumArray```|Write down number array|
|```StringArray```|Write down string array|
|```OrderText```|Write order of number|
|```BigNum```|Format big numbers|

##### Maths functions #####

|**Name**|**Description**|
|---|---|
|```numGen```|Random number generator|
|```modifySameDigits```|Modify same digits|
|```shuffleAssoc```|Associative array shuffle|
|```convertRoman```|Convert to Roman number|
|```newNum```|Generate new random number based on given number|
|```hasDigit```|Check if number has digit|
|```replaceDigit```|Replace digit in number|
|```gcd```|Get greatest common divisor of two numbers|
|```combos```|Combinations with repetitions|
|```recursiveSeries```|Get member of series defined by recursive formula|
|```equationAddition```|Generate equation for addition|
|```placeValues```|Place values|
|```polarToCartesian```|Transform polar coordinates to Cartesian|
|```divisors```|Define divisors of a number|
|```binomial_coeff```|binomial coefficient|
|```fact```|factorial|
|```stdev```|standard deviation|
|```toRad```|Convert deg to rad|
|```toDeg```|Convert rad to deg|
|```round1```|Round number to given precision|
|```round2```|Round number to given precision and replace '.' with ','|
|```lcm```|Least common multiple of numbers "a" and "b"|
|```primefactor```|Prime factorization|

##### Draw functions #####

|**Name**|**Description**|
|---|---|
|```DrawLine```|Draw line|
|```DrawText```|Draw text|
|```DrawRectangle```|Draw rectangle|
|```DrawCircle```|Draw circle|
|```DrawPolygon```|Draw polygon|
|```DrawPath```|Draw path|
|```DrawVector```|Draw vector|
|```DrawArc```|Draws an arc between P1, P2, P3 (P1 is the center)|
|```DrawPieChart```|Pie slice between P1, P2, P3 (P1 is the center)|
|```PerpendicularIntersect```|Calculates intersection between line1 containing A and B, and line2 containing C perpendicular to line1.|
|```Triangle```|Calculates third point of triangle given by two points and angles|
|```IntersectLines```|Calculates intersect of two lines|
|```LinePoint```|Calculate point along line|
|```Translate```|Translate point with length in direction|
|```Rotate```|Rotate point P around center C with angle alpha|
|```Length```|Length of vector|

# Licensing

Copyright (c) 2015 Zsebtanár
The exercise framework is [MIT licenced](https://en.wikipedia.org/wiki/MIT_License).
The exercises are under a [Creative Commons by-nc-sa license](http://creativecommons.org/licenses/by-nc-sa/4.0/deed.hu).

# Contact

- Website: http://www.zsebtanar.hu
- Email: zsebtanar@gmail.com
