//This's the object that has to be executed
Ext.application (
{
    name   : 'MyApp',
    //This is the function that has to be executed and showed in the window of the html page
    launch : function()
    {
        var myStore = new Ext.data.JsonStore(
        {
            // Store configuration
            storeId: 'myJSONstore',
            proxy:
            {
                type: 'ajax',
                url: 'data_search.php',
                reader:
                {
                    type: 'json'
                },
                actionMethods:
                {
                    read : 'POST'
                }
            },
            fields:
            [
                {
                    name: 'vorname'
                },
                {
                    name: 'name'
                },
                {
                    name: 'geburtsdatum',
                    type: 'date',
                    dateFormat: 'd.m.Y'
                },
                {
                    name: 'geburtsort'
                }
            ],
            //Doesn't directly load the store (it works because the store is created dinamically)
            autoLoad: false
        } );

        //Definition of the store for the cities listed in the ComboBox
        var comboStore = Ext.create('Ext.data.Store',
        {
            fields: ['abbr', 'city'],
            data : [
                {"abbr":"BE", "city":"Berlin"},
                {"abbr":"ER", "city":"Erice"},
                {"abbr":"MI", "city":"Milano"},
                {"abbr":"NY", "city":"New York"},
                {"abbr":"PA", "city":"Palermo"},
                {"abbr":"SB", "city":"Saarbruecken"},
                {"abbr":"VA", "city":"Valencia"}
            ]
        } );

        var form_search = Ext.create('Ext.form.Panel',
        {
            title: 'Suchen',
            //This layout have the fields divided into 2 columns, each with 2 fields (one under the other)
            layout: 'hbox',
            items:
            [   //To have 2 fields in each column I add them into 2 containers (the columns),
                //each with a vbox layout (to have the fields one under the other)
                {
                    xtype: 'container',
                    layout: 'vbox',
                    margin: 20,
                    items:
                    [
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Vorname',
                            name: 'vorname'
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Name',
                            name: 'name',
                            top: 10
                        }
                    ]
                },
                {
                    xtype: 'container',
                    layout: 'vbox',
                    margin: 20,
                    items:
                    [
                        {
                            xtype: 'datefield',
                            fieldLabel: 'Datum',
                            name: 'datum',
                            maxValue: new Date()  //Date that can be added is maximum the current date
                        },
                        {
                            xtype: 'combobox',
                            fieldLabel: 'St&auml;dte',
                            name: 'ort',
                            store: comboStore,
                            queryMode: 'remote',  //In which way the comboStore is token:
                                                  //'remote' means that comboStore is loaded dinamically by ComboBox
                            displayField: 'city', //What is shown in the selection list
                            valueField: 'city',   //How to refer to the elements in the list
                            top: 10
                        }
                    ]
                }
            ],
            buttons:
            [
                {
                    text: 'Auswahl anwenden',
                    listeners:
                    {
                        click: function(button)
                        {
                            //Send the values added in the fields of form_search and these are of type FormData
                            var infos = form_search.getValues();
                            console.log(button);
                            myStore.load(
                            {
                                params: infos
                            } );
                        }
                    }
                }
            ]
        } );

        //Control with the function control_infos if there are infos at least
        //for the fields vorname, name and datum and if these infos have a correct type
        //(vorname and name must be string and datum must have the format 'm/d/Y')
        function control_infos(infos)
        {
            //For this purpose use regular expressions
            var exp_string = new RegExp(/[a-z]/i);
            var exp_datum = new RegExp(/\d{2}\/\d{2}\/\d{4}/i);
            if (!exp_string.test(infos.vorname))
            {
                Ext.Msg.alert('The added Vorname is not valid');
                return false;
            }
            if (!exp_string.test(infos.name))
            {
                Ext.Msg.alert('The added Name is not valid');
                return false;
            }
            if (!exp_datum.test(infos.datum))
            {
                Ext.Msg.alert('The added Geburtsdatum is not valid');
                return false;
            }
            return ( (exp_string.test(infos.vorname)) && (exp_string.test(infos.name))
                    && (exp_datum.test(infos.datum)) );
        };

        //Definition of the form to do a search request to the DB
        var form_add = Ext.create('Ext.form.Panel',
        {
            title: 'Beif&uuml;gen',
            //This layout have the fields divided into 2 columns, each with 2 fields (one under the other)
            layout: 'hbox',
            items:
            [   //To have 2 fields in each column I add them into 2 containers (the columns),
                //each with a vbox layout (to have the fields one under the other)
                {
                    xtype: 'container',
                    layout: 'vbox',
                    margin: 20,
                    items:
                    [
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Vorname',
                            name: 'vorname'
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Name',
                            name: 'name'
                        }
                    ]
                },
                {
                    xtype: 'container',
                    layout: 'vbox',
                    margin: 20,
                    items:
                    [
                        {
                            xtype: 'datefield',
                            fieldLabel: 'Datum',
                            name: 'datum',
                            maxValue: new Date()  //Date that can be added is maximum the current date
                        },
                        {
                            xtype: 'combobox',
                            fieldLabel: 'St&auml;dte',
                            name: 'ort',
                            store: comboStore,
                            queryMode: 'remote',  //In which way the comboStore is token:
                                                  //'remote' means that comboStore is loaded dinamically by ComboBox
                            displayField: 'city', //What is shown in the selection list
                            valueField: 'city'   //How to refer to the elements in the list
                        }
                    ]
                }
            ],
            buttons:
            [
                {
                    text: 'Ok',
                    listeners:
                    {
                        click: function(button)
                        {
                            //Send the values added in the fields of form_add and these are of type FormData
                            var infos = form_add.getForm().getValues();
                            if (control_infos(infos))
                            {
                                Ext.Ajax.request(
                                {
                                    url: 'data_add.php',
                                    method: 'POST',
                                    params: infos,
                                    success: function(response, opts)
                                    {
                                        var resp = Ext.decode(response.responseText);
                                        myStore.load();
                                        console.log('Store loaded after giving informations', myStore);
                                    },
                                    failure: function(response, opts)
                                    {
                                        Ext.Msg.alert('Server-side failure with status code ' + response.status);
                                    }
                                } );
                            }
                        }
                    }
                },
                {
                    text: 'L&ouml;schen',
                    listeners:
                    {
                        click: function(button)
                        {
                            form_add.reset();
                        }
                    }
                }
            ]
        } );

        //The container has inside: the table, the submit botton and other objects
        var container = Ext.create ('Ext.panel.Panel',
        {
            renderTo: Ext.getBody(),
            title: 'Neue Firma',
            header:
            {
                cls: 'my-header'
            },
            //Layout usefull when an item has to take all the space as soon as the other item is collapsed
            layout: 'border',
            width: 1280,
            height: 900,
            border: 1,
            style:
            {
                borderColor: 'black',
                borderStyle: 'solid',
                borderWidth: '1px'
            },
            defaults:
            {
                labelWidth: 200,
                style:
                {
                    padding: '10px',
                    // position: 'absolute'
                }
            },
            items:
            [
                // header,
            {   //Definition of the tab which contains the two forms (one for searching and the other for adding)
                xtype: 'tabpanel',
                title: 'Um mit der Tabelle zu bearbeiten',
                region: 'north',  //It means that this item is positioned up in the container
                collapsible: true,
                height: 250,
                layout: 'fit',
                items:
                [
                    form_search,
                    form_add
                ]
            },
            {   //Define characteristics of the table as a grid
                xtype: 'grid',
                title: 'Mitarbeiter Tabelle',
                region: 'center',  //It means that this item is the main one in the container
                store: myStore,
                name: 'table',
                layout: 'fit',
                columns:
                {
                    items:
                    [
                        {
                            text: 'Vorname',
                            dataIndex: 'vorname'
                        },
                        {
                            text: 'Name',
                            dataIndex: 'name'
                        },
                        {
                            text: 'Geburtsdatum',
                            dataIndex: 'geburtsdatum',
                            renderer: Ext.util.Format.dateRenderer('d.m.Y')
                        },
                        {
                            text: 'Geburtsort',
                            dataIndex: 'geburtsort'
                        }
                    ],
                    defaults:
                    {
                        flex: 1
                    }
                },
                width: 450,
                height: 300,
                fbar:
                [
                {
                    type: 'button',
                    text: 'Tabelle laden',
                    listeners:
                    {
                        click: function(button)
                        {
                            myStore.load();
                            console.log('Store after load', myStore);
                        }
                    }
                }
                ]
            }
            ]
        } );
    }
} );
