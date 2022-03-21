import { combineReducers, createStore, applyMiddleware } from "redux";
import thunk from "redux-thunk";

import { Logger } from "./types";
import marvel from "./marvel";
import moviedb from "./moviedb";
import pokemon from "./pokemon";

const reducer = combineReducers({ marvel, moviedb, pokemon });

const logger: Logger =
  ({ getState }) =>
  (next) =>
  (action) => {
    console.group(action.type);
    console.info("dispatching", action);
    const result = next(action);
    console.log("next state", getState());
    console.groupEnd();
    return result;
  };

const store = createStore(
  reducer,
  applyMiddleware(
    ...[...(process.env.NODE_ENV === "development" ? [logger] : []), thunk]
  )
);

export { store };
