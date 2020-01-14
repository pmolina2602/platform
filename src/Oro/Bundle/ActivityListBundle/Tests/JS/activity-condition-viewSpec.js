define(function(require) {
    'use strict';

    const $ = require('jquery');
    const _ = require('underscore');
    const filters = require('./Fixture/filters.json');
    const listOptions = JSON.stringify(require('./Fixture/list-options.json'));
    const FieldConditionView = require('oroquerydesigner/js/app/views/field-condition-view');
    const ActivityConditionView = require('oroactivitylist/js/app/views/activity-condition-view');
    require('jasmine-jquery');

    xdescribe('oroactivitylist/js/app/views/activity-condition-view', function() {
        let activityConditionView;

        describe('without initial value', function() {
            beforeEach(function(done) {
                activityConditionView = new ActivityConditionView({
                    autoRender: true,
                    filters: filters,
                    listOptions: _.clone(listOptions)
                });
                window.setFixtures(activityConditionView.$el);
                $.when(activityConditionView.deferredRender).then(function() {
                    done();
                });
            });

            afterEach(function() {
                activityConditionView.dispose();
            });

            it('is ascendant of FieldConditionView', function() {
                expect(activityConditionView).toEqual(jasmine.any(FieldConditionView));
            });

            it('has correct value after filters are set', function(done) {
                const activityTypes = ['Oro_Bundle_TaskBundle_Entity_Task'];
                activityConditionView.setActivityExistence('hasNotActivity');
                activityConditionView.setActivityTypes(activityTypes);
                activityConditionView.setChoiceInputValue('$createdAt').then(function() {
                    const newFilterValue = {
                        type: '1',
                        part: 'value',
                        value: {
                            start: '2016-01-01 00:00',
                            end: '2017-01-01 00:00'
                        }
                    };
                    activityConditionView.filter.setValue(newFilterValue);
                    const conditionValue = activityConditionView.getValue();
                    expect(conditionValue.criterion.data.filterType).toEqual('hasNotActivity');
                    expect(conditionValue.criterion.data.activityType.value).toEqual(activityTypes);
                    expect(conditionValue.criterion.data.activityFieldName).toBe('$createdAt');
                    expect(conditionValue.criterion.data.filter.data).toEqual(newFilterValue);
                    done();
                });
            });
        });

        describe('with initial value', function() {
            let activityConditionView;
            const initialValue = {
                criterion: {
                    filter: 'activityList',
                    data: {
                        activityFieldName: '$updatedAt',
                        filterType: 'hasNotActivity',
                        activityType: {
                            value: [
                                'Oro_Bundle_TaskBundle_Entity_Task',
                                'Oro_Bundle_CallBundle_Entity_Call'
                            ]
                        },
                        filter: {
                            filter: 'datetime',
                            data: {
                                type: '2',
                                part: 'value',
                                value: {
                                    start: '2017-09-01 00:00',
                                    end: '2017-10-01 00:00'
                                }
                            }
                        }
                    }
                }
            };
            beforeEach(function(done) {
                activityConditionView = new ActivityConditionView({
                    autoRender: true,
                    filters: filters,
                    value: initialValue,
                    listOptions: listOptions
                });
                window.setFixtures(activityConditionView.$el);
                $.when(activityConditionView.deferredRender).then(function() {
                    done();
                });
            });

            afterEach(function() {
                activityConditionView.dispose();
            });

            it('renders a correct field', function() {
                const choiceInputValue = activityConditionView.getChoiceInputValue();
                expect(choiceInputValue).toBe('$updatedAt');
            });
            it('shows a filter with value', function() {
                const filterValue = activityConditionView.filter.getValue();
                expect(filterValue.value).toEqual({start: '2017-09-01 00:00', end: '2017-10-01 00:00'});
            });
        });
    });
});
