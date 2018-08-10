class SearchJSON {
  // Return an array of objects according to key, value, or key and value matching.
  static objects(obj, key, val) {
    let objects = [];
    for (let i in obj) {
      if (!obj.hasOwnProperty(i))
        continue;
      if (typeof obj[i] == 'object')
        objects = objects.concat(SearchJSON.objects(obj[i], key, val));
      else if (i == key && obj[i].includes(val) || i == key && val == '')
        objects.push(obj + 'found');
      else if (obj[i] == val && key == '')
        if (objects.lastIndexOf(obj) == -1)
          objects.push(obj);
    }
    return objects;
  }
  // Return an array of values that match a certain key.
  static values(obj, key) {
    let objects = [];
    for (let i in obj) {
      if (!obj.hasOwnProperty(i))
        continue;
      if (typeof obj[i] == 'object')
        objects = objects.concat(SearchJSON.values(obj[i], key));
      else if (i == key)
        objects.push(obj[i]);
    }
    return objects;
  }
  // Return an array of keys that match on a certain value.
  static keys(obj, val) {
    let objects = [];
    for (let i in obj) {
      if (!obj.hasOwnProperty(i))
        continue;
      if (typeof obj[i] == 'object')
        objects = objects.concat(SearchJSON.keys(obj[i], val));
      else if (obj[i] == val)
        objects.push(i);
    }
    return objects;
  }

  static match(json, needle) {
    let found = [];
    let re = new RegExp(needle, 'i');
    json.forEach((item, ix) => {
      Object.keys(item).forEach(key => {
        if (typeof item[key] !== 'string')
          return;
        if (item[key].match(re))
          if (found.indexOf(ix) === -1)
            found.push(ix);
      });
    });
    return { searched: needle, indexes: found }
  }
}
