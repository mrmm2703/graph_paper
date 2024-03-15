import { Graph, ObjectExt, Cell } from '@antv/x6';

// The Reference Node
Graph.registerNode(
  'reference',
  {
    inherit: 'rect',
    markup: [
      {
        tagName: 'rect',
        selector: 'body',
      },
      {
        tagName: 'rect',
        selector: 'name-rect',
      },
      {
        tagName: 'rect',
        selector: 'attrs-rect',
      },
      {
        tagName: 'rect',
        selector: 'methods-rect',
      },
      {
        tagName: 'text',
        selector: 'name-text',
      },
      {
        tagName: 'text',
        selector: 'attrs-text',
      },
      {
        tagName: 'text',
        selector: 'methods-text',
      },
    ],
    attrs: {
      rect: {
        width: 160,
      },
      body: {
        stroke: '#fff',
      },
      'name-rect': {
        fill: '#5f95ff',
        stroke: '#fff',
        strokeWidth: 0.5,
      },
      'attrs-rect': {
        fill: '#eff4ff',
        stroke: '#fff',
        strokeWidth: 0.5,
      },
      'name-text': {
        ref: 'name-rect',
        refY: 0.5,
        refX: 0.5,
        textAnchor: 'middle',
        fontWeight: 'bold',
        fill: '#fff',
        fontSize: 12,
      },
      'attrs-text': {
        ref: 'attrs-rect',
        refY: 0.5,
        refX: 5,
        textAnchor: 'left',
        fill: 'black',
        fontSize: 10,
      },
    },
    propHooks(meta) {
      const { library_item_id, ...others } = meta

      console.log("otehrs")
      console.log(others)
      console.log(meta)

      if (!(library_item_id)) {
        return meta
      }

      
      const rects = [
        { type: 'name', text: [
          lib_items[library_item_id].source_title,
          lib_items[library_item_id].authors_short_str,
        ] },
        { type: 'attrs', text: [
          lib_items[library_item_id].published_year,
          lib_items[library_item_id].publisher,
          lib_items[library_item_id].source_type,
        ] },
      ]

      let offsetY = 0
      rects.forEach((rect) => {
        const height = rect.text.length * 12 + 16
        ObjectExt.setByPath(
          others,
          `attrs/${rect.type}-text/text`,
          rect.text.join('\n'),
        )
        ObjectExt.setByPath(others, `attrs/${rect.type}-rect/height`, height)
        ObjectExt.setByPath(
          others,
          `attrs/${rect.type}-rect/transform`,
          'translate(0,' + offsetY + ')',
        )
        offsetY += height
      })

      others.size = { width: 160, height: offsetY }

      return others
    },
  },
  true,
)


Graph.registerEdge(
    'arrow',
    {
      inherit: 'edge',
      attrs: {
        line: {
          strokeWidth: 1,
          targetMarker: {
            name: 'path',
            d: 'M 6 10 L 18 4 C 14.3333 6 10.6667 8 7 10 L 18 16 z',
            fill: 'black',
            offsetX: -5,
          },
        },
      },
    },
    true,
  )


const graph = new Graph({
  container: document.getElementById('container'),
  connecting: {
    router: 'metro'
  }
})

function load_map(data) {
  console.log(data)
    const cells = []
    const edgeShapes = [
      'arrow'
    ]
    data.forEach(item => {
      if (edgeShapes.includes(item.shape)) {
        cells.push(graph.createEdge(item))
      } else {
        cells.push(graph.createNode(item))
      }
    })
    // cells.push(graph.createNode({
    //   "shape": "reference",
    //   "library_item_id": 9,
    //   "position": {
    //     "x": 300,
    //     "y": 40
    //   }
    // }))
    graph.resetCells(cells)
    graph.zoomToFit({ padding: 10, maxScale: 1 })
}

console.log(db_map_data)
if (db_map_data != "") {
  load_map(JSON.parse(db_map_data))
}

var reference_picker = document.getElementById("reference_picker")
for (var id in lib_items) {
    var option = document.createElement("option");
    option.value = lib_items[id].library_item_id;
    option.innerHTML = lib_items[id].source_title + " (" + lib_items[id].published_year + ")";
    reference_picker.appendChild(option);
}

document.getElementById("insert-btn").onclick = () => {
  graph.addNode({
    "shape": "reference",
    "library_item_id": reference_picker.value,
    "position": {
      "x": 300,
      "y": 40
    },
    "attrs": {
      "library_item_id": reference_picker.value
    }
  })
}

document.getElementById("save-btn").onclick = () => {
  const searchParams = new URLSearchParams(window.location.search)

  let map_str = JSON.stringify(get_map_json())
  console.log(map_str)
  var url = 'save_map.php';
  var form = $("<form action='" + url + "' method='post'>" +
    "<input type='text' name='map_data' value='" + map_str + "' />" +
    "<input type='text' name='map_id' value='" + searchParams.get('map_id') + "' />" +
    "<input type='text' name='project_name' value='" + searchParams.get('project_name') + "' />" +
    "<input type='text' name='project_id' value='" + searchParams.get('project_id') + "' />" +
    "</form>");
  $('body').append(form);
  form.submit();
}

var select1 = null;
var select2 = null;
var linkText = "";
var isSelecting = false;
document.getElementById("link-btn").onclick = () => {
  linkText = prompt("Enter link description (leave empty for none)")
  isSelecting = true;
}

graph.on('node:click', ({e, x, y, node, view}) => {
  console.log(node);
  if (isSelecting) {
    if (select1 == null) {
      select1 = node.id;
    } else if (select2 == null) {
      select2 = node.id;
      console.log(linkText);
      graph.addEdge({
        "shape": "arrow",
        "source": select1,
        "target": select2,
        "label": linkText,
      });
      isSelecting = false;
      select1 = null;
      select2 = null;
    }
  }
})

graph.on('edge:click', ({e, x, y, edge, view}) => {
  console.log(edge);
})

console.log(graph)

function get_map_json() {
  let save_obj = []
  graph.getNodes().forEach(node => {
    let cur_node = {
      id: node.id,
      shape: node.shape,
      library_item_id: node.getAttrByPath("library_item_id"),
      position: node.getPosition(),
      attrs: {
        library_item_id: node.getAttrByPath("library_item_id"),
      }
    }
    save_obj.push(cur_node)
  })

  graph.getEdges().forEach(edge => { 
    console.log(edge)
    let cur_edge = {
      id: edge.id,
      shape: edge.shape,
      source: edge.getSourceCellId(),
      target: edge.getTargetCellId(),
      label: (edge.getLabels().length > 0 ? edge.getLabelAt(0).attrs.label.text : "")
    }
    save_obj.push(cur_edge)
  })
  console.log(save_obj)
  return save_obj
}