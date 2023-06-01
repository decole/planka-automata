# Planka automation

- [x] For Planka cards with a certain label "Project Card", number automatically with a prefix `[<prefix>-<number>]`.
- [ ] Transferring cards from column Archive to another board with the Archive/Trash column.
- [ ] WebHook for Sentry, create a card in the project with the label "Bugfix".
- [ ] Notify in telegram about a suitable deadline by card.

----

Used by RoadRunner

https://github.com/Baldinof/roadrunner-bundle

----

* Download RoadRunner locally: `vendor/bin/rr get --location bin/`
* Run your application: `bin/rr serve -c .rr.dev.yaml --debug`
* For production, use: `bin/rr serve`
* Visit http://localhost:85 <- see [docker-compose.yaml.dist](docker-compose.yaml.dist)

----

## Number automatically with a prefix

In `.env` file copy to `.env.local` and change variable:

- `PLANKA_BOARD_ID`
- `PLANKA_BUGFIX_LABEL_ID`
- `PLANKA_NUMERICAL_LABEL_ID`

This ids you find, by seen in your Planka service. Watch Crome Browser Ctrl+Shift+I. 
And in tab "Network" see `?__sails_io_sdk_version=....`, click this line, and tab "Messages" see payloads. 
![img.png](docs/img/01img.png)

![img.png](docs/img/03img.png)

In payload **body** see in **'item'** field **name**. 
If this name equal name your current board - you find needed boardId. 
See you `PLANKA_BOARD_ID` is `745435921242915851` in example. Find in payload 

Then you create label for numerate cards. Example create **'in project'** label
When you create card and labeling this card by label **'in project'**, you see in tab "Message"
next payload:

![img.png](docs/img/02img.png)

You find `PLANKA_NUMERICAL_LABEL_ID` is `data` -> `labelId: "770114546592384237"`.
So `PLANKA_NUMERICAL_LABEL_ID=770114546592384237`

For Bugfix cards, create lebel **'bugfix'**

Set the card to a new label **'bugfix'** and you will see its identifier.
Accordingly, `PLANKA_BUGFIX_LABEL_ID` will be equal to the found identifier.

> ! First of all run the migrations `bin/console d:m:m --no-interaction`

Next, you need to configure the launch of the cron command `bin/console cli:planka`

The first time the script will go through, find your cards, find out the last card number by your prefix.

Adds a label to cards that already have a prefixed number without a label.
And save the last occupied card number for the prefix to the database.

After that, you can put the numbered cards into other boards or delete them.
The script will know which last card number to rely on.

## Transferring cards from column Archive to another board with the Archive/Trash column.

### What is it for?

If your cards are already ready and there is an archive column on your board, in which 
a lot of cards are being accumulated. Then this column will grow and overflow. 
This is very inconvenient and a separate script was created to gradually empty this column. 
Script, a week after placing the card in the column, transfers the archive to another beard where 
there is a special column for cards from the archive of the current board.

----
