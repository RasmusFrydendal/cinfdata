
"""This script is used during development of plugins"""

from __future__ import print_function

import sys
import argparse
import os
import json
from os import path

THISDIR = path.dirname(path.realpath(__file__))

# TIME: 16:45-17:15, 21:05-23:05

def parse_args():

    parser = argparse.ArgumentParser(description='...')
    parser.add_argument('setup', help='...')
    parser.add_argument('type', help='...')
    parser.add_argument('left', help='...')
    parser.add_argument('--plugin-name', '-n', default=None, help='...')
    parser.add_argument('--plugin-input', '-i', default=None, help='...')

    parser.add_argument('--port', '-p', type=int, default=None,
                        help='...')

    args = parser.parse_args()
    return args


def get_data(args):
    """Return the data"""
    # Initilize global graphsettings
    from graphsettings import graphSettings
    global_graph_settings = graphSettings(args.type)

    # Initialize database backend
    from databasebackend import dataBaseBackend

    # Fill out options (these would normally come from the webpage)
    left_plot_list = [int(id_) for id_ in args.left.split(',')]
    print('Left plot list:', left_plot_list)
    options = {
        'type': args.type,
        'from_to': (None, None),
        'left_plotlist': left_plot_list,
        'right_plotlist': [],
        'as_function_of': False,
        'diff_left_y': False,
        'diff_right_y': False,
    }
    for n in range(3):
        options['linscale_x' + str(n)] = False
        options['linscale_left_y' + str(n)] = False
        options['linscale_right_y' + str(n)] = False

    #if args.plugin_name:
    #    options['plugin_settings'] = {}
    #    options['plugin_settings']['Test47'] = {
    #        'activated': True,
    #        'input': '11',
    #    }
    # Allow using port forward
    kwargs = {}
    if args.port:
        print('MySQL: Using port forward to local host port {}'\
              .format(args.port))
        kwargs.update({'host': '127.0.0.1', 'port': args.port})
    
    database_backend = dataBaseBackend(options, global_graph_settings, **kwargs)

    # Create row in plot_com_out
    output_id = -1
    if args.plugin_name:
        output_id = plugin_config(args, database_backend, global_graph_settings)

    data = database_backend.get_data()

    # If necessary get output
    if output_id > -1:
        query = 'SELECT output FROM plot_com_out WHERE id={0}'
        database_backend.cursor.execute(query.format(output_id))
        output = database_backend.cursor.fetchone()[0]
        output = json.loads(output)
    else:
        output = {}

    return data, output


def plugin_config(args, database_backend, global_graph_settings):
    """"""
    plugin_settings = global_graph_settings['plugins'][args.plugin_name]
    expects_output = plugin_settings.get('output') in ('raw', 'html')
    if expects_output:
        query = 'INSERT INTO plot_com_out (output) values ("")'
        database_backend.cursor.execute(query)
        output_id = database_backend.conn.insert_id()
    else:
        output_id = -1

    input_ = {'output_id': output_id}
    
    # Fill in input
    if args.plugin_name:
        input_[args.plugin_name] = {'activate': 'checked'}
        if args.plugin_input:
            input_[args.plugin_name]['input'] = args.plugin_input

    input_as_json = json.dumps(input_)
    query = 'INSERT INTO plot_com_in (input) values (%s)'
    database_backend.cursor.execute(query, (input_as_json,))
    database_backend.o['input_id'] = database_backend.conn.insert_id()

    return output_id


def main():
    # Parse command line arguments
    args = parse_args()

    # Change current working dir
    setup_dir = path.join(THISDIR, args.setup)
    os.chdir(setup_dir)
    print('CWD:', os.getcwd())
    sys.path.insert(1, setup_dir)

    # Get data
    data, output = get_data(args)

    if args.plugin_name:
        out_str = output.get(args.plugin_name)
        if out_str.startswith('<pre>') and out_str.endswith('</pre>'):
            out_str = out_str[5: -6]
        if out_str:
            print("######### OUTPUT #########")
            print(out_str)
            print("######### OUTPUT #########")
    


main()
