define(
	[
		'emberjs',
		'text!./DiagramEditor.html'
	],
	function(Ember, template) {

		return Ember.View.extend({
			template: Ember.Handlebars.compile(template),

			targetUrl: function() {
				return '/neos/mxgraph?diagramNode=' + this.get('inspector.nodeSelection.selectedNode.nodePath');
			}.property('inspector.nodeSelection.selectedNode.nodePath')
		});
	});