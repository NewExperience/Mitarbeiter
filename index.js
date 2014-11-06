//This's the object that has to be executed
Ext.application (
{
    name   : 'MyApp',
    //This is the function that has to be executed and showed in the window of the html page
    launch : function()
    {
        //The grid shows 3 Mitarbeiter per page
        var itemsPerPage = 3;

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
                    type: 'json',
                    //The data that has to add are in the array 'items' of the json object
                    root: 'items',
                    //Total number of elements in the store (so that can fix the paging)
                    totalProperty: 'total'
                },
                actionMethods:
                {
                    read   : 'POST'
                }
            },
            fields:
            [
                {
                    name: 'Vorname'
                },
                {
                    name: 'Name'
                },
                {
                    name: 'Geburtsdatum',
                    type: 'date',
                    dateFormat: 'd.m.Y'
                },
                {
                    name: 'Geburtsort'
                }
            ],
            //Doesn't directly load the store (it works because the store is created dinamically)
            autoLoad: false,
            //It says how many Mitarbeiter has to be shown in each page of the paging
            pageSize: itemsPerPage,
            //It doesn't sort, but sends infos (as params) to the php to which the store is connected
            remoteSort: true,
            listeners:
            {
                //Fix the params of the store with the infos added in the form_search fields
                beforeload: function(myStore)
                {
                    myStore.params = form_search.getForm().getValues();
                }
            }
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
                            name: 'Vorname'
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Name',
                            name: 'Name',
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
                            name: 'Geburtsdatum',
                            maxValue: new Date()  //Date that can be added is maximum the current date
                        },
                        {
                            xtype: 'combobox',
                            fieldLabel: 'St&auml;dte',
                            name: 'Geburtsort',
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
                            //Add start and limit as params to send how many elements per page has to be shown
                            infos.start = 0;
                            infos.limit = itemsPerPage;
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
            if (!exp_string.test(infos.Vorname))
            {
                Ext.Msg.alert('The added Vorname is not valid');
                return false;
            }
            if (!exp_string.test(infos.Name))
            {
                Ext.Msg.alert('The added Name is not valid');
                return false;
            }
            if (!exp_datum.test(infos.Geburtsdatum))
            {
                Ext.Msg.alert('The added Geburtsdatum is not valid');
                return false;
            }
            return ( (exp_string.test(infos.Vorname)) && (exp_string.test(infos.Name))
                    && (exp_datum.test(infos.Geburtsdatum)) );
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
                            name: 'Vorname'
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Name',
                            name: 'Name'
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
                            name: 'Geburtsdatum',
                            maxValue: new Date()  //Date that can be added is maximum the current date
                        },
                        {
                            xtype: 'combobox',
                            fieldLabel: 'St&auml;dte',
                            name: 'Geburtsort',
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
                                infos.start = 0;
                                infos.limit = itemsPerPage;
                                Ext.Ajax.request(
                                {
                                    url: 'data_add.php',
                                    method: 'POST',
                                    params: infos,
                                    success: function(response, opts)
                                    {
                                        myStore.load();
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

        //The window with the form to be shown when I right-click on a row
        //I have to pass to myWindow the infos written in the row that I clicked
        var myWindow = Ext.create ('Ext.window.Window',
        {
            title: '&Auml;nderungen',
            layout: 'fit',
            height: 300,
            width: 500,
            closeAction: 'hide', //It means that I can reuse the window
            // cls: 'my-window',
            items:
            {
                xtype: 'form',
                items:
                [
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Vorname',
                        name: 'Vorname',
                        margin: '15, 10, 10, 15'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Name',
                        name: 'Name',
                        margin: '15, 10, 10, 15'
                    },
                    {
                        xtype: 'datefield',
                        fieldLabel: 'Datum',
                        name: 'Geburtsdatum',
                        maxValue: new Date(),
                        margin: '15, 10, 10, 15'
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: 'St&auml;dte',
                        name: 'Geburtsort',
                        store: comboStore,
                        queryMode: 'remote',
                        displayField: 'city',
                        valueField: 'city',
                        editable: true,
                        typeAhead: true,
                        selectOnFocus: true,
                        margin: '15, 10, 10, 15'
                    }
                ]
            },
            buttons:
            [
                {
                    text: 'Ok',
                    listeners:
                    {
                        click: function(button)
                        {
                            var myForm = myWindow.down('form');
                            var infos = myForm.getForm().getValues();
                            //I add to the infos object the id field
                            infos.ID = myWindow.params.id;
                            infos.start = 0;
                            infos.limit = itemsPerPage;
                            Ext.Ajax.request(
                            {
                                url: 'data_update.php',
                                method: 'POST',
                                params: infos,
                                success: function(response, opts)
                                {
                                    myStore.load();
                                },
                                failure: function(response, opts)
                                {
                                    Ext.Msg.alert('Server-side failure with status code ' + response.status);
                                }
                            } );
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
                    padding: '10px'
                }
            },
            items:
            [
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
                            dataIndex: 'Vorname'
                        },
                        {
                            text: 'Name',
                            dataIndex: 'Name'
                        },
                        {
                            text: 'Geburtsdatum',
                            dataIndex: 'Geburtsdatum',
                            renderer: Ext.util.Format.dateRenderer('d.m.Y')
                        },
                        {
                            text: 'Geburtsort',
                            dataIndex: 'Geburtsort'
                        }
                    ],
                    defaults:
                    {
                        flex: 1
                    }
                },
                //Toolbar at the bottom of the grid to show the pages
                dockedItems:
                [
                    {
                        xtype: 'pagingtoolbar',
                        store: myStore,
                        dock: 'bottom',
                        displayInfo: true
                    }
                ],
                width: 450,
                height: 300,
                viewConfig:  //To give red colour to the Mitarbeiter without a Geburtsort added
                {
                    getRowClass: function(record, index)
                    {
                        if (record.get('Geburtsort')==='')
                        return 'my-red-row';
                    }
                },
                listeners:
                {
                    itemcontextmenu: function(grid, record, item, index, event)
                    {
                        var myForm = myWindow.down('form');
                        myForm.loadRecord(record);
                        //Insertion of the date and of the ort not directly, as for vorname and name
                        //because the type of the field isn't textfield
                        var date = record.get('Geburtsdatum');
                        myForm.getForm().findField('Geburtsdatum').setValue(date);
                        var ort = record.get('Geburtsort');
                        myForm.getForm().findField('Geburtsort').setValue(ort);
                        var ID = record.get('ID');
                        //I send this ID to the window as a parameter
                        myWindow.params = { id: ID };
                        myWindow.showAt(event.getXY());
                    },
                    //Event that manages the click on a column's title about which I've to sort
                    //I wouldn't need it because it's done by default, but if I specify
                    //I can manage this event with the search request
                    headerclick: function(column)
                    {
                        var infos = form_search.getForm().getValues();
                        //Add start and limit as params to send how many elements per page has to be shown
                        infos.start = 0;
                        infos.limit = itemsPerPage;
                        myStore.load(
                        {
                            params: infos
                        } );
                    }
                },
                fbar:
                [
                {
                    type: 'button',
                    text: 'Tabelle laden',
                    listeners:
                    {
                        click: function(button)
                        {
                            myStore.load(
                            {
                                params:
                                {
                                    start: 0,
                                    limit: itemsPerPage
                                }
                            } );
                        }
                    }
                }
                ]
            }
            ]
        } );
    }
} );
