import manifest from '@neos-project/neos-ui-extensibility';
import DiagramEditor from './DiagramEditor';

manifest('Sandstorm.MxGraph', {}, (globalRegistry) => {
  const editorsRegistry = globalRegistry.get('inspector').get('editors');

  editorsRegistry.set('Sandstorm.MxGraph/Editors/DiagramEditor', {
    component: DiagramEditor
  });
});
