# Draw.io - Reusable Diagrams

## Goal

Diagrams should be **referenceable** by different pages, instead of being **copied**.

## Idea 1: attach to **ImageInterface**

Benefits:
- **Asset Usage** can be used to track duplicates.

Notes:
- There is a workflow to replace assets, by "Replace Asset Resource", BUT this will DIRECTLY
  replace the LIVE WORKSPACE ASSET; so it will break workspace encapsulation.

## Idea 1a: via separate domain model

This was the original idea; but using a subclass of Image or Asset would be even more convenient I think.

## Idea 2: Custom referencing, manual updates

Assume we have a property "identifier" on the Diagram node, with the following rules:

- when updating the diagram, we update the diagram on **all nodes with the same identifier**
- when updating the identifier, you get an autocomplete for existing identifiers in the system.
  - we can use https://github.com/sandstorm/LazyDataSource here for this (for server side search)
  - ... and a new entry "create new" (or s.th. like it)
- If the identifier changes TO ANOTHER EXISTING identifier, we copy the other diagram over to this node.
  - If there are multiple with the same identifier, we use the **newest** one.
  - `nodePropertyChanged` event

- You get an indicator how often this identifier is used. With NodeType View??


## ToDo

- [x] Autocomplete for identifier
  - [x] LazyDataSource
  - [ ] some issues in re-rendering in LazyDataSource
- [x] if Identifier changed and you press APPLY, the diagram is updated.
- [WONT] if Identifier changed and you open the diagram editor, the other diagram is shown
  - !!! we don't do this, as it is too easy to accidentally overwrite stuff.
- [x] when saving a diagram with non-empty identifier, update all diagram targets
- [x] a new identifier is generated on creation
- [ ] display number of references
