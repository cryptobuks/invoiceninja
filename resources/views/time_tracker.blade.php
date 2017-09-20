@extends('master')

@section('head')
	@parent

    <link href="{{ asset('css/built.css') }}?no_cache={{ NINJA_VERSION }}" rel="stylesheet" type="text/css"/>

@stop

@section('head_css')
    @parent

    <style type="text/css">

		#side-nav {
		  position: fixed;
		  width: 400px;
		  height:100vh;
		  left: 0;
		  right: 0;
		  overflow-y: auto;
		  padding-right: 1px;
		  top: 80px;
		  xbackground-color: red;
		}

		#content-wrapper {
		  margin: 60px 0px 0 400px;
		  padding: 0 30px;
		  overflow-y: auto;
		  position: fixed;
		  left: 0;
		  top: 20px;
		  height:100vh;
		}

		button .glyphicon {
			vertical-align: text-top;
		}

        a:focus {
            outline: none;
        }

		.list-group-item.active {
			background-color: #f8f8f8 !important;
			color: black !important;
			border-left-color: #f8f8f8 !important;
			border-right-color: #f8f8f8 !important;
			box-shadow: 0 0 0 2px rgba(0,0,0,.1), 0 2px 2px rgba(0,0,0,.2);
    		border-color: #fff !important;
		}

        span.link {
            cursor:pointer;
            color:#337ab7;
            text-decoration:none;
        }

        span.link:hover {
            text-decoration:underline;
        }

        .no-gutter > [class*='col-'] {
            padding-right:0;
            padding-left:0;
        }

        .list-group-item:before {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 6px;
            content: "";
        }

        .list-group-item-type1:before { background-color: #1c9f77; }
        .list-group-item-type2:before { background-color: #d95d02; }
        .list-group-item-type3:before { background-color: #716cb1; }
        .list-group-item-type4:before { background-color: #e62a8b; }
        .list-group-item-type5:before { background-color: #5fa213; }
        .list-group-item-type6:before { background-color: #e6aa04; }
        .list-group-item-type7:before { background-color: #a87821; }
        .list-group-item-type8:before { background-color: #676767; }

    </style>

@stop

@section('body')
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-collapse" style="padding-top:12px; padding-bottom:12px;">

                <!-- Navbar Buttons -->
                <ul class="nav navbar-right" style="margin-right:0px; padding-left:12px; float:right;">
                    <span data-bind="text: selectedTask().duration, visible: selectedTask" class="hidden-xs"
                        style="font-size:28px; color:white; padding-right:12px; vertical-align:middle; display:none;"></span>
                    <button type='button' data-bind="click: onStartClick, css: startClass" class="btn btn-lg">
                        <span data-bind="text: startLabel"></span>
                        <span data-bind="css: startIcon"></span>
                    </button>
                </ul>

                <!-- Navbar Filter -->
                <div class="input-group input-group-lg">
                    <span class="input-group-addon" style="width:1%;"><span class="glyphicon glyphicon-time"></span></span>
                    <input type="search" class="form-control search" autocomplete="off" autofocus="autofocus"
                        data-bind="event: { focus: onFilterFocus, input: onFilterChanged, keypress: onFilterKeyPress }, value: filter, valueUpdate: 'afterkeydown', attr: {placeholder: placeholder}">
                </div>

            </div>
        </div>
    </nav>

    <div style="height:74px"></div>

	<!--
	<div data-bind="text: ko.toJSON(model.selectedTask().client_id)"></div>
	<div data-bind="text: ko.toJSON(model.selectedTask().client)"></div>
	<div data-bind="text: ko.toJSON(model.selectedTask().project_id)"></div>
	<div data-bind="text: ko.toJSON(model.selectedTask().project)"></div>
	-->

	<div>
		<div id="side-nav">

			<!-- Task List -->
			<div data-bind="foreach: filteredTasks">
				<a href="#" data-bind="click: $parent.selectTask, event: { mouseover: showActionButton, mouseout: hideActionButton }, css: listItemState"
					class="list-group-item" stylex="white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
					<div class="pull-right" style="text-align:right;">
						<div data-bind="visible: actionButtonVisible()"
							data-bindx="style : { visibility : actionButtonVisible() ? '' : 'hidden' }">
							&nbsp;&nbsp;
							<button type="button" data-bind="css: startClass, click: onStartClick, clickBubble: false"
								class="btn btn-sm" style="padding-left:0px; padding-right: 12px; padding-bottom: 6px; margin-top:5px;">
								<span data-bind="css: startIcon"></span>
							</button>
						</div>
					</div>
					<div class="pull-right" style="text-align:right">
						<div data-bind="text: duration, style: { fontWeight: isRunning() ? 'bold' : '' }"></div>
						<div data-bind="text: age, style: { fontWeight: isRunning() ? 'bold' : '' }" style="padding-top: 2px"></div>
					</div>
					<h4 class="list-group-item-heading">
						<span data-bind="text: description.truncated, style: { fontWeight: isRunning() ? 'bold' : '' }"></span>&nbsp;
					</h4>
					<p class="list-group-item-text">
						<span class="link" data-bind="text: clientName, click: $parent.viewClient, clickBubble: false"></span>
						<span data-bind="visible: clientName &amp;&amp; projectName"> | </span>
						<span class="link" data-bind="text: projectName, click: $parent.viewProject, clickBubble: false"></span>
						&nbsp;
					</p>
				</a>
			</div>


		</div>
		<div id="content-wrapper">
			<div class="container-fluid">

				<!-- Task Form -->
                <div data-bind="visible: selectedTask" style="margin:20px; display:none;">
                    <div class="panel panel-default">
                        <div class="panel-body">
							<form id="taskForm">
								<span data-bind="event: { keypress: onFormKeyPress, change: onFormChange, input: onFormChange }">
									<div style="padding-bottom: 20px" class="client-select">
			                            {!! Former::select('client_id')
												->addOption('', '')
												->label('client')
												->data_bind("dropdown: selectedTask().client_id") !!}
									</div>
									<div style="padding-bottom: 20px" class="project-select">
			                            {!! Former::select('project_id')
			                                    ->addOption('', '')
			                                    ->data_bind("dropdown: selectedTask().project_id")
			                                    ->label(trans('texts.project')) !!}
									</div>
		                            {!! Former::textarea('description')
		                                    ->data_bind("value: selectedTask().description")
		                                    ->rows(4) !!}
								</span>

								<center style="padding-top: 30px">
									<span data-bind="visible: showArchive">
										{!! DropdownButton::normal(trans('texts.archive'))
											->withAttributes([
												'class' => 'archive-dropdown',
											])
											->large()
											->withContents([
											  ['label' => trans('texts.delete_task'), 'url' => '#'],
											]
										  )->split() !!}
									</span>
									{!! Button::normal(trans('texts.cancel'))
										->appendIcon(Icon::create('remove-circle'))
										->withAttributes([
											'data-bind' => 'click: onCancelClick, visible: showCancel',
										])
										->large() !!}
									&nbsp;
									{!! Button::success(trans('texts.save'))
											->large()
											->appendIcon(Icon::create('floppy-disk'))
											->withAttributes([
												'data-bind' => 'click: onSaveClick, css: { disabled: ! formChanged() }',
											]) !!}
								</center>
							</form>
                        </div>
                    </div>
                </div>

			</div>
		</div>
	</div>


    <div class="container" style="margin: 0 auto;width: 100%;">
        <div class="row no-gutter">

        </div>
    </div>

    <script type="text/javascript">

        var tasks = {!! $tasks !!};
		var clients = {!! $clients !!};
	    var projects = {!! $projects !!};
        var dateTimeFormat = '{{ $account->getMomentDateTimeFormat() }}';
        var timezone = '{{ $account->getTimezone() }}';

        function ViewModel() {
            var self = this;
            self.tasks = ko.observableArray();
            self.filter = ko.observable('');
            self.selectedTask = ko.observable(false);
            self.clock = ko.observable(0);
			self.formChanged = ko.observable(false);

			self.onSaveClick = function() {
				if (! model.selectedTask() || ! model.formChanged()) {
					return;
				}
				var data = $('#taskForm').serialize();
				var task = model.selectedTask();
		        data += '&time_log=' + JSON.stringify(task.times());
				var url = '{{ url('/tasks') }}';
				var method = 'post';
				if (task.public_id()) {
					method = 'put';
					url += '/' + task.public_id();
				}
				$.ajax({
					dataType: 'json',
					type: method,
					data: data,
					url: url,
					accepts: {
						json: 'application/json'
					},
					success: function(response) {
						console.log(response);
						var task = self.selectedTask();
						if (task.isNew()) {
							//self.addTask(task);
						} else {
							//self.removeTask(task.original);
							//self.addTask(task);
						}
						task.update(response);
						self.selectTask(task);
					},
				});
			}

			self.onCancelClick = function() {
				sweetConfirm(function() {
					var task = self.selectedTask();
					if (task.isNew()) {
						self.selectedTask(false);
						$('.search').focus();
					} else {
						task.update(task.data);
					}
					self.formChanged(false);
				});

				return false;
			}

            self.onFilterFocus = function(data) {
                self.selectedTask(false);
            }

            self.onFilterChanged = function(data) {
                self.selectedTask(false);
            }

			self.onFilterKeyPress = function(data, event) {
                if (event.which == 13) {
                    self.onStartClick();
                }
                return true;
            }

			self.onFormChange = function(data, event) {
				self.formChanged(true);
                return true;
            }

			self.onFormKeyPress = function(data, event) {
                if (event.which == 13) {
                    self.onSaveClick();
                }
                return true;
            }

            self.viewClient = function(task) {
                self.filter(task.client().displayName());
                return false;
            }

            self.viewProject = function(task) {
                self.filter(task.project().name());
                return false;
            }

            self.onStartClick = function() {
                if (self.selectedTask()) {
                    self.selectedTask().onStartClick();
					/*
					if (self.selectedTask().original) {
						self.selectedTask().original.onStartClick();
					}
					*/
                } else {
                    var time = new TimeModel();
                    time.startTime(moment().unix());
                    var task = new TaskModel();
                    task.description(self.filter());
                    task.addTime(time);
                    self.selectedTask(task);
					self.addTask(task);
                    self.filter('');
					$('.client-select input.form-control').focus();
                }
            }

            self.tock = function(startTime) {
                self.clock(self.clock() + 1);
                setTimeout(function() {
                    model.tock();
                }, 1000);
            }

			self.showArchive = ko.computed(function() {
				var task = self.selectedTask();
				if (! task) {
					return false;
				}
				return task.isCreated() && ! self.formChanged();
			});

			self.showCancel = ko.computed(function() {
				var task = self.selectedTask();
				if (! task) {
					return false;
				}
				return task.isNew() || self.formChanged();
			});

            self.startIcon = ko.computed(function() {
                if (self.selectedTask()) {
                    return self.selectedTask().startIcon();
                } else {
                    return 'glyphicon glyphicon-play';
                }
            });

            self.startLabel = ko.computed(function() {
                if (self.selectedTask()) {
                    if (self.selectedTask().isRunning()) {
                        return "{{ trans('texts.stop') }}";
                    } else {
                        return "{{ trans('texts.resume') }}";
                    }
                } else {
                    return "{{ trans('texts.start') }}";
                }
            });

            self.startClass = ko.computed(function() {
                if (self.selectedTask()) {
                    return self.selectedTask().startClass();
                } else {
                    return 'btn-success';
                }
            });

            self.placeholder = ko.computed(function() {
				return "{{ trans('texts.what_are_you_working_on') }}";
            });

            self.filteredTasks = ko.computed(function() {

				// filter the data
                if(! self.filter()) {
                    var tasks = self.tasks();
                } else {
                    var filtered = ko.utils.arrayFilter(self.tasks(), function(task) {
                        return task.matchesFilter(self.filter());
                    });
                    var tasks = filtered.length == 0 ? self.tasks() : filtered;
                }

				// sort the data
				tasks.sort(function (left, right) {
					return right.createdAt() - left.createdAt();
					/*
					right = right.firstTime() ? right.firstTime().order() : right.createdAt();
					left = left.firstTime() ? left.firstTime().order() : left.createdAt();
					return right - left;
					*/
				});

				return tasks;
            });

            self.addTask = function(task) {
                self.tasks.push(task);
            }

			self.removeTask = function(task) {
                self.tasks.remove(task);
            }

            self.selectTask = function(task) {
				// if a client is selected the project list will be filtered
				// this prevents the new task's project from being show
				// to fix it we're clearing the list and then firing a
				// client change event to re-filter the list
				refreshProjectList(true);

				//var clone = new TaskModel(task.data);
				//clone.original = task;
				//self.selectedTask(clone);

				self.selectedTask(task);
				//self.filter('');

				if (! task.project()) {
					$('select#client_id').trigger('change');
				}

				self.formChanged(false);
            }
        }

        function TaskModel(data) {
            var self = this;
			self.public_id = ko.observable();
            self.description = ko.observable('');
            self.time_log = ko.observableArray();
			self.client_id = ko.observable();
			self.project_id = ko.observable();
            self.client = ko.observable();
            self.project = ko.observable();
            self.actionButtonVisible = ko.observable(false);
			self.created_at = ko.observable(moment().format('YYYY-MM-DD HH:mm:ss'));

            self.mapping = {
				'client': {
					update: function(data) {
						if (! data.data) {
							self.client_id(0);
							return false;
						} else {
							self.client_id(data.data.public_id);
                        	return new ClientModel(data.data);
						}
                    }
                },
                'project': {
					update: function(data) {
						if (! data.data) {
							self.project_id(0);
							return false;
						} else {
							self.project_id(data.data.public_id);
                        	return new ProjectModel(data.data);
						}
                    },
                },
				'ignore': [
					'time_log',
					'client_id',
					'project_id',
				]
            }

			self.update = function(data) {
				self.data = data;
				var times = JSON.parse(data.time_log);
				data.time_log = false;
				ko.mapping.fromJS(data, self.mapping, this);
				self.time_log.removeAll();
				for (var i=0; i<times.length; i++) {
                    self.time_log.push(new TimeModel(times[i]));
                }
			}

            if (data) {
				self.update(data);
            }

			self.isNew = ko.computed(function() {
				return ! self.public_id();
			});

			self.isCreated = ko.computed(function() {
				return self.public_id();
			});

            self.showActionButton = function() {
                self.actionButtonVisible(true);
            }

            self.hideActionButton = function() {
                self.actionButtonVisible(false);
            }

            self.addTime = function(time) {
                self.time_log.push(time);
            }

			self.times = function() {
				var times = [];
				for (var i=0; i<self.time_log().length; i++) {
		            var timeLog = self.time_log()[i];
		            if (! timeLog.isEmpty()) {
		                times.push([timeLog.startTime(),timeLog.endTime()]);
		            }
				}
				return times;
			}

            self.matchesFilter = function(filter) {
                filter = filter.toLowerCase();
				var parts = filter.split(' ');
				for (var i=0; i<parts.length; i++) {
					var part = parts[i];
					var isMatch = false;
	                if (self.description().toLowerCase().indexOf(part) >= 0) {
	                    isMatch = true;
	                }
	                if (self.project()) {
						var projectName = self.project().name();
						if (projectName && projectName.toLowerCase().indexOf(part) >= 0) {
	                    	isMatch = true;
						}
	                }
	                if (self.client()) {
						var clientName = self.client().displayName();
						if (clientName && clientName.toLowerCase().indexOf(part) >= 0) {
	                    	isMatch = true;
						}
	                }
					if (! isMatch) {
						return false;
					}
				}
                return true;
            }

            self.onStartClick = function() {
                if (self.isRunning()) {
                    var time = self.lastTime();
                    time.endTime(moment().unix());
                } else {
                    var time = new TimeModel();
                    time.startTime(moment().unix());
                    self.addTime(time);
                }
            }

            self.listItemState = ko.computed(function() {
				var str = '';
				if (self == model.selectedTask()) {
					str = 'active';
				}
                if (! self.project()) {
                    return str;
                }
                var projectId = self.project().public_id();
                var colorNum = (projectId-1) % 8;
                return str + ' list-group-item-type' + (colorNum+1);

            });

            self.isRunning = ko.computed(function() {
				var timeLog = self.time_log();
                if (! timeLog.length) {
                    return false;
                }
                var time = timeLog[timeLog.length-1];
                return time.isRunning();
            });

			self.clientName = ko.computed(function() {
				return self.client() ? self.client().displayName() : '';
			});

			self.projectName = ko.computed(function() {
				return self.project() ? self.project().name() : '';
			});

            self.startClass = ko.computed(function() {
                return self.isRunning() ? 'btn-danger' : 'btn-success';
            });

            self.startIcon = ko.computed(function() {
                return self.isRunning() ? 'glyphicon glyphicon-stop' : 'glyphicon glyphicon-play';
            });

			self.description.truncated = ko.computed(function() {
				return truncate(self.description(), self.actionButtonVisible() ? 35 : 60);
            });

			self.createdAt = function() {
				return moment(self.created_at()).unix();
			}

			self.firstTime = function() {
				return self.time_log()[0];
			}

			self.lastTime = function() {
				var times = self.time_log();
				return times[times.length-1];
			}

            self.age = ko.computed(function() {
                if (! self.time_log().length) {
                    return '';
                }
                var time = self.firstTime();
                return time.age();
            });

            self.duration = ko.computed(function() {
                model.clock(); // bind to the clock
                if (! self.time_log().length) {
                    return '0:00:00';
                }
                var time = self.lastTime();
                var now = new Date().getTime();
                var duration = 0;
                if (time.isRunning()) {
                    var duration = now - (time.startTime() * 1000);
                    duration = Math.floor(duration / 100) / 10;
                } else {
                    self.time_log().forEach(function(time){
                        duration += time.duration();
                    });
                }

                var duration = moment.duration(duration * 1000);
                return Math.floor(duration.asHours()) + moment.utc(duration.asMilliseconds()).format(":mm:ss");
            });
        }

        function ProjectModel(data) {
            var self = this;
            self.name = ko.observable('');
			self.public_id = ko.observable(0);

            if (data) {
                ko.mapping.fromJS(data, {}, this);
            }
        }

        function ClientModel(data) {
            var self = this;
            self.public_id = ko.observable(0);
            self.name = ko.observable('');
            self.contacts = ko.observableArray();

            self.mapping = {
                'contacts': {
                    create: function(options) {
                        return new ContactModel(options.data);
                    }
                }
            }

            self.displayName = ko.computed(function() {
                if (self.name()) {
                    return self.name();
                }
                if (self.contacts().length == 0) return;
                var contact = self.contacts()[0];
                if (contact.first_name() || contact.last_name()) {
                    return contact.first_name() + ' ' + contact.last_name();
                } else {
                    return contact.email();
                }
            });

            if (data) {
                ko.mapping.fromJS(data, self.mapping, this);
            }
        }

        function ContactModel(data) {
            var self = this;
            self.first_name = ko.observable('');
            self.last_name = ko.observable('');
            self.email = ko.observable('');

            if (data) {
                ko.mapping.fromJS(data, {}, this);
            }

            self.displayName = ko.computed(function() {
                if (self.first_name() || self.last_name()) {
                    return (self.first_name() || '') + ' ' + (self.last_name() || '') + ' ';
                } else if (self.email()) {
                    return self.email();
                } else {
                    return '';
                }
            });
        }

        function TimeModel(data) {
            var self = this;
            self.startTime = ko.observable(0);
            self.endTime = ko.observable(0);
            self.duration = ko.observable(0);
            self.actionsVisible = ko.observable(false);
            self.isStartValid = ko.observable(true);
            self.isEndValid = ko.observable(true);

            if (data) {
                self.startTime(data[0]);
                self.endTime(data[1]);
            };

			self.order = ko.computed(function() {
				return self.startTime();
			});

            self.isEmpty = ko.computed(function() {
                return !self.startTime() && !self.endTime();
            });

            self.isRunning = ko.computed(function() {
                return self.startTime() && !self.endTime();
            });

            self.age = ko.computed(function() {
                return moment.unix(self.startTime()).fromNow();
            });

            self.duration = ko.computed(function() {
                return self.endTime() - self.startTime();
            });


            /*
            self.startTime.pretty = ko.computed({
                read: function() {
                    return self.startTime() ? moment.unix(self.startTime()).tz(timezone).format(dateTimeFormat) : '';
                },
                write: function(data) {
                    self.startTime(moment(data, dateTimeFormat).tz(timezone).unix());
                }
            });

            self.endTime.pretty = ko.computed({
                read: function() {
                    return self.endTime() ? moment.unix(self.endTime()).tz(timezone).format(dateTimeFormat) : '';
                },
                write: function(data) {
                    self.endTime(moment(data, dateTimeFormat).tz(timezone).unix());
                }
            });

            self.setNow = function() {
                self.startTime(moment.tz(timezone).unix());
                self.endTime(moment.tz(timezone).unix());
            }

            self.duration.pretty = ko.computed({
                read: function() {
                    var duration = false;
                    var start = self.startTime();
                    var end = self.endTime();

                    if (start && end) {
                        var duration = end - start;
                    }

                    var duration = moment.duration(duration * 1000);
                    return Math.floor(duration.asHours()) + moment.utc(duration.asMilliseconds()).format(":mm:ss");
                },
                write: function(data) {
                    self.endTime(self.startTime() + convertToSeconds(data));
                }
            });
            */
        }

		function refreshProjectList(forceClear) {
			var clientId = $('input[name=client_id]').val();
			$projectCombobox = $('select#project_id');
			$projectCombobox.find('option').remove().end().combobox('refresh');
			$projectCombobox.append(new Option('', ''));
			@if (Auth::user()->can('create', ENTITY_PROJECT))
				if (clientId) {
					$projectCombobox.append(new Option("{{ trans('texts.create_project')}}: $name", '-1'));
				}
			@endif

			var list = (clientId && ! forceClear) ? (projectsForClientMap.hasOwnProperty(clientId) ? projectsForClientMap[clientId] : []).concat(projectsForAllClients) : projects;

			for (var i=0; i<list.length; i++) {
				var project = list[i];
				$projectCombobox.append(new Option(project.name,  project.public_id));
			}
			$('select#project_id').combobox('refresh');
		}


		var clientMap = {};
		var projectMap = {};
		var projectsForClientMap = {};
		var projectsForAllClients = [];

        $(function() {

			// setup clients and project comboboxes
			var $clientSelect = $('select#client_id');

			for (var i=0; i<projects.length; i++) {
				var project = projects[i];
				projectMap[project.public_id] = project;

				var client = project.client;
				if (!client) {
					projectsForAllClients.push(project);
				} else {
					if (!projectsForClientMap.hasOwnProperty(client.public_id)) {
						projectsForClientMap[client.public_id] = [];
					}
					projectsForClientMap[client.public_id].push(project);
				}
			}

			for (var i=0; i<clients.length; i++) {
				var client = clients[i];
				clientMap[client.public_id] = client;
			}

			$clientSelect.append(new Option('', ''));
			for (var i=0; i<clients.length; i++) {
				var client = clients[i];
				var clientName = getClientDisplayName(client);
				if (!clientName) {
					continue;
				}
				$clientSelect.append(new Option(clientName, client.public_id));
			}

			$clientSelect.combobox();
			$clientSelect.on('change', function(e) {
				var clientId = $('input[name=client_id]').val();
				var projectId = $('input[name=project_id]').val();
				var client = clientMap[clientId];
				var project = projectMap[projectId];
				if (!clientId && (window.model && !model.selectedTask().client())) {
					e.preventDefault();return;
				}
				if (window.model && model.selectedTask()) {
					model.selectedTask().client(new ClientModel(client));
					model.selectedTask().client_id(clientId);
					model.selectedTask().project_id(0);
					model.selectedTask().project(false);
				}
				refreshProjectList();
			});

			var $projectSelect = $('select#project_id').on('change', function(e) {
				$clientCombobox = $('select#client_id');
				var projectId = $('input[name=project_id]').val();
				if (projectId == '-1') {
					$('input[name=project_name]').val(projectName);
					//var project = new ProjectModel();
					//model.selectedTask().project = project;
					//model.selectedTask().project_id(projectId);
				} else if (projectId) {
					var project = projectMap[projectId];
					model.selectedTask().project(new ProjectModel(project));
					model.selectedTask().project_id(projectId);
					// when selecting a project make sure the client is loaded
					if (project && project.client) {
						var client = clientMap[project.client.public_id];
						if (client) {
							project.client = client;
							model.selectedTask().client(new ClientModel(client));
							model.selectedTask().client_id(client.public_id);
						}
					}
				} else {
					$clientSelect.trigger('change');
				}
			});

			@include('partials/entity_combobox', ['entityType' => ENTITY_PROJECT])

			$clientSelect.trigger('change');

			window.model = new ViewModel();
			for (var i=0; i<tasks.length; i++) {
				var task = tasks[i];
				var taskModel = new TaskModel(task);
				model.addTask(taskModel);
			}
			ko.applyBindings(model);
			model.tock();
        });

    </script>

@stop