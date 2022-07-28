function template({template}, opts, {imports, interfaces, componentName, jsx, exports}) {
  const plugins = ['jsx', 'typescript']
  const typeScriptTpl = template.smart({plugins})
  jsx.openingElement.name.name = 'Icon'
  jsx.closingElement.name.name = 'Icon'
  jsx.openingElement.attributes = [
    ...jsx.openingElement.attributes.filter((attr) => attr.name.name !== 'width' && attr.name.name !== 'height'),
    {
      type: 'JSXAttribute',
      name: {type: 'JSXIdentifier', name: '{...props}'},
    },
  ]
  if (jsx.children[0].openingElement.name.name === 'path') {
    jsx.children[0].openingElement.attributes = jsx.children[0].openingElement.attributes.map((attr) =>
      attr.name.name === 'fill' ? {...attr, value: {type: 'StringLiteral', value: 'currentColor'}} : attr
    )
  }

  return typeScriptTpl.ast`
    ${imports}
    import {Icon, IconProps} from '@chakra-ui/react'
    ${interfaces}
    
    const ${componentName} = (props: IconProps) => ${jsx}
    
    ${exports}
  `
}

module.exports = template
